# 文章模块接口

这版接口先解决 4 个基础能力：

- 上传文章
- 后端自动分段分句
- 前端获取结构化阅读数据
- 原音频地址返回

统一返回格式：

```json
{
  "code": 0,
  "message": "success",
  "data": {}
}
```

## 1. 获取文章列表

`GET /api/articles`

返回字段重点：

- `id`
- `title`
- `slug`
- `author`
- `level`
- `resource_type`
- `word_count`
- `has_audio`

## 2. 上传文章

`POST /api/articles`

支持 `multipart/form-data`

字段：

- `title` 必填
- `content` 必填，原始英文文章全文
- `author` 可选
- `source` 可选
- `level` 可选
- `resource_type` 可选，`text|audio|video`
- `accent` 可选，`US|UK`
- `total_duration` 可选，单位秒
- `audio_file` 可选，支持 `mp3/wav/m4a/aac/ogg`

成功后会自动：

- 生成 `slug`
- 统计 `word_count`
- 将全文按自然段拆分
- 将每段按句子拆分
- 写入 `article_segments`

## 3. 获取文章详情

`GET /api/articles/{id}`

返回文章基础信息，不展开完整阅读结构。

## 4. 获取阅读结构

`GET /api/articles/{id}/reading`

返回示例：

```json
{
  "code": 0,
  "message": "success",
  "data": {
    "article_id": 1,
    "title": "How Bridges Stay Strong",
    "audio_url": "/storage/articles/audio/demo.mp3",
    "paragraphs": [
      {
        "paragraph_index": 0,
        "text": "Sentence one. Sentence two.",
        "sentences": [
          {
            "id": 1,
            "sentence_index": 0,
            "text": "Sentence one.",
            "translation": null,
            "start_time": null,
            "end_time": null
          }
        ]
      }
    ]
  }
}
```

这个接口就是前端阅读页最直接可用的数据源。

## 5. 获取音频信息

`GET /api/articles/{id}/audio`

返回字段：

- `has_audio`
- `audio_url`
- `accent`
- `total_duration`

## 6. 更新文章

`PUT /api/articles/{id}`

可更新上传接口中的大多数字段。

如果更新了 `content`，系统会重新分段分句并覆盖旧的 `article_segments`。

如果重新上传了 `audio_file`，系统会替换旧音频。

## 7. 删除文章

`DELETE /api/articles/{id}`

会同时删除：

- 文章本身
- 关联的 `article_segments`
- 已上传的本地音频文件

## 当前联调建议

前端没开始并不会卡住后端开发，可以先这样测：

- Postman 测接口
- `php artisan test` 跑自动化测试
- `php artisan migrate --seed` 造一篇样例文章

如果要让浏览器直接播放本地上传音频，还需要执行一次：

```bash
php artisan storage:link
```
