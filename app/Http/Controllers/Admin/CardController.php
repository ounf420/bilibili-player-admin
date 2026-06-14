<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Card;
use Illuminate\Http\Request;

class CardController extends Controller
{
    public function index(Request $request)
    {
        $query = Card::query()->with('user');

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }
        if ($batchId = $request->get('batch_id')) {
            $query->where('batch_id', $batchId);
        }
        if ($keyword = $request->get('keyword')) {
            $query->where(function ($q) use ($keyword) {
                $q->where('card_no', 'like', "%{$keyword}%")
                  ->orWhere('card_secret', 'like', "%{$keyword}%");
            });
        }

        $cards = $query->latest()->paginate(20);
        $batches = Card::whereNotNull('batch_id')->distinct()->pluck('batch_id');

        return view('admin.card.index', compact('cards', 'batches'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'count' => 'required|integer|min:1|max:1000',
            'remark' => 'nullable|string|max:255'
        ]);

        $batchId = time();
        $cards = [];

        for ($i = 0; $i < $request->count; $i++) {
            $cards[] = [
                'card_no' => Card::generateCardNo(),
                'card_secret' => Card::generateSecret(),
                'amount' => $request->amount,
                'status' => 'unused',
                'batch_id' => $batchId,
                'remark' => $request->remark,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        Card::insert($cards);

        return redirect()->route('admin.cards.index')
            ->with('success', "成功生成 {$request->count} 张卡密，面值 ¥{$request->amount}，批次号：{$batchId}");
    }

    public function disable(Request $request, Card $card)
    {
        if ($card->status === 'used') {
            return back()->with('error', '已使用的卡密不能禁用');
        }

        $card->update(['status' => 'disabled']);
        return back()->with('success', '卡密已禁用');
    }

    public function enable(Request $request, Card $card)
    {
        if ($card->status === 'used') {
            return back()->with('error', '已使用的卡密不能启用');
        }

        $card->update(['status' => 'unused']);
        return back()->with('success', '卡密已启用');
    }

    public function export(Request $request)
    {
        $query = Card::where('status', 'unused');

        if ($batchId = $request->get('batch_id')) {
            $query->where('batch_id', $batchId);
        }

        $cards = $query->get();
        
        $csv = "卡号,卡密,面值,批次号,备注\n";
        foreach ($cards as $card) {
            $csv .= "{$card->card_no},{$card->card_secret},{$card->amount},{$card->batch_id},{$card->remark}\n";
        }

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename=cards_' . date('YmdHis') . '.csv');
    }
}
