<?php

namespace App\Http\Controllers;

use App\Models\CompanionShopItem;
use App\Services\CompanionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CompanionController extends Controller
{
    public function __construct(
        protected CompanionService $companionService
    ) {
    }

    public function index(): View
    {
        return view('companion.index', $this->companionService->getPagePayload(request()->user()));
    }

    public function purchase(CompanionShopItem $item): RedirectResponse
    {
        $result = $this->companionService->purchaseItem(request()->user(), $item);

        return redirect()
            ->route('companion.index')
            ->with($result['success'] ? 'status' : 'error', $result['message']);
    }

    public function equip(CompanionShopItem $item): RedirectResponse
    {
        $result = $this->companionService->equipItem(request()->user(), $item);

        return redirect()
            ->route('companion.index')
            ->with($result['success'] ? 'status' : 'error', $result['message']);
    }
}
