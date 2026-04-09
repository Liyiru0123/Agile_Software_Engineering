import fs from 'node:fs';
import path from 'node:path';
import process from 'node:process';
import { fileURLToPath } from 'node:url';
import { WebSocketServer, WebSocket } from 'ws';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const projectRoot = path.resolve(__dirname, '..');
const envPath = path.join(projectRoot, '.env');

function loadEnvFile(filePath) {
    if (!fs.existsSync(filePath)) {
        return {};
    }

    const lines = fs.readFileSync(filePath, 'utf8').split(/\r?\n/);
    const values = {};

    for (const line of lines) {
        const trimmed = line.trim();
        if (!trimmed || trimmed.startsWith('#')) {
            continue;
        }

        const separator = trimmed.indexOf('=');
        if (separator === -1) {
            continue;
        }

        const key = trimmed.slice(0, separator).trim();
        let value = trimmed.slice(separator + 1).trim();

        if (
            (value.startsWith('"') && value.endsWith('"'))
            || (value.startsWith("'") && value.endsWith("'"))
        ) {
            value = value.slice(1, -1);
        }

        values[key] = value;
    }

    return values;
}

const fileEnv = loadEnvFile(envPath);
const env = {
    ...fileEnv,
    ...process.env,
};

const apiKey = env.DASHSCOPE_API_KEY || '';
const baseUrl = env.DASHSCOPE_REALTIME_BASE_URL || 'wss://dashscope.aliyuncs.com/api-ws/v1/realtime';
const defaultModel = env.DASHSCOPE_REALTIME_MODEL || 'qwen3.5-omni-flash-realtime';
const host = env.DASHSCOPE_REALTIME_PROXY_HOST || '127.0.0.1';
const port = Number(env.DASHSCOPE_REALTIME_PROXY_PORT || 8787);
const routePath = '/' + String(env.DASHSCOPE_REALTIME_PROXY_PATH || '/ws').replace(/^\/+/, '');

if (!apiKey) {
    console.error('[qwen-realtime-proxy] DASHSCOPE_API_KEY is missing. Add it to .env first.');
    process.exit(1);
}

const server = new WebSocketServer({
    host,
    port,
    path: routePath,
});

console.log(`[qwen-realtime-proxy] listening on ws://${host}:${port}${routePath}`);
console.log(`[qwen-realtime-proxy] upstream base: ${baseUrl}`);
console.log(`[qwen-realtime-proxy] default model: ${defaultModel}`);

server.on('connection', (client, request) => {
    const origin = request.headers.origin || 'unknown-origin';
    const browserUrl = new URL(request.url || routePath, `ws://${request.headers.host || `${host}:${port}`}`);
    const model = browserUrl.searchParams.get('model') || defaultModel;
    const upstreamUrl = `${baseUrl}?model=${encodeURIComponent(model)}`;

    console.log(`[qwen-realtime-proxy] browser connected from ${origin} -> ${upstreamUrl}`);

    const upstream = new WebSocket(upstreamUrl, {
        headers: {
            Authorization: `Bearer ${apiKey}`,
        },
    });

    const upstreamQueue = [];

    client.on('message', (message, isBinary) => {
        if (upstream.readyState === WebSocket.OPEN) {
            upstream.send(message, { binary: isBinary });
            return;
        }

        upstreamQueue.push({ message, isBinary });
    });

    client.on('close', () => {
        if (upstream.readyState === WebSocket.OPEN || upstream.readyState === WebSocket.CONNECTING) {
            upstream.close(1000, 'Browser disconnected.');
        }
    });

    client.on('error', (error) => {
        console.error('[qwen-realtime-proxy] browser socket error:', error.message);
    });

    upstream.on('open', () => {
        console.log(`[qwen-realtime-proxy] upstream connected for model ${model}`);
        while (upstreamQueue.length > 0) {
            const item = upstreamQueue.shift();
            if (!item) {
                break;
            }

            upstream.send(item.message, { binary: item.isBinary });
        }
    });

    upstream.on('message', (message, isBinary) => {
        if (client.readyState === WebSocket.OPEN) {
            client.send(message, { binary: isBinary });
        }
    });

    upstream.on('close', (code, reason) => {
        const detail = Buffer.isBuffer(reason) ? reason.toString('utf8') : String(reason || '');
        console.log(`[qwen-realtime-proxy] upstream closed (${code}) ${detail}`);
        if (client.readyState === WebSocket.OPEN) {
            client.close(1000, 'Upstream closed.');
        }
    });

    upstream.on('error', (error) => {
        console.error('[qwen-realtime-proxy] upstream error:', error.message);
        if (client.readyState === WebSocket.OPEN) {
            client.send(JSON.stringify({
                type: 'error',
                error: {
                    message: `Proxy upstream error: ${error.message}`,
                },
            }));
            client.close(1011, 'Upstream error.');
        }
    });
});

server.on('error', (error) => {
    console.error('[qwen-realtime-proxy] server error:', error.message);
    process.exit(1);
});
