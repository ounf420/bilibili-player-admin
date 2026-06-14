@extends('admin.layouts.app')

@section('title', '卡密管理')

@section('content')
<div class="content-header">
    <h1>卡密管理</h1>
    <div>
        <button class="btn btn-success" data-toggle="modal" data-target="#generateModal">
            <i class="fas fa-plus"></i> 生成卡密
        </button>
        <a href="{{ route('admin.cards.export', request()->query()) }}" class="btn btn-info">
            <i class="fas fa-download"></i> 导出未使用
        </a>
    </div>
</div>

<!-- 统计卡片 -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-info"><i class="fas fa-credit-card"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">总卡密数</span>
                <span class="info-box-number">{{ \App\Models\Card::count() }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-success"><i class="fas fa-check-circle"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">未使用</span>
                <span class="info-box-number">{{ \App\Models\Card::where('status', 'unused')->count() }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-warning"><i class="fas fa-exchange-alt"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">已使用</span>
                <span class="info-box-number">{{ \App\Models\Card::where('status', 'used')->count() }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-danger"><i class="fas fa-ban"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">已禁用</span>
                <span class="info-box-number">{{ \App\Models\Card::where('status', 'disabled')->count() }}</span>
            </div>
        </div>
    </div>
</div>

<!-- 筛选 -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="form-inline">
            <div class="form-group mr-2">
                <select name="status" class="form-control">
                    <option value="">全部状态</option>
                    <option value="unused" {{ request('status') === 'unused' ? 'selected' : '' }}>未使用</option>
                    <option value="used" {{ request('status') === 'used' ? 'selected' : '' }}>已使用</option>
                    <option value="disabled" {{ request('status') === 'disabled' ? 'selected' : '' }}>已禁用</option>
                </select>
            </div>
            <div class="form-group mr-2">
                <select name="batch_id" class="form-control">
                    <option value="">全部批次</option>
                    @foreach($batches as $batch)
                        <option value="{{ $batch }}" {{ request('batch_id') == $batch ? 'selected' : '' }}>
                            批次 {{ $batch }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group mr-2">
                <input type="text" name="keyword" class="form-control" placeholder="搜索卡号/卡密" value="{{ request('keyword') }}">
            </div>
            <button type="submit" class="btn btn-primary mr-2">筛选</button>
            <a href="{{ route('admin.cards.index') }}" class="btn btn-secondary">重置</a>
        </form>
    </div>
</div>

<!-- 卡密列表 -->
<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>卡号</th>
                    <th>卡密</th>
                    <th>面值</th>
                    <th>状态</th>
                    <th>使用者</th>
                    <th>使用时间</th>
                    <th>批次</th>
                    <th>备注</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cards as $card)
                <tr>
                    <td><code>{{ $card->card_no }}</code></td>
                    <td><code>{{ $card->card_secret }}</code></td>
                    <td>¥{{ number_format($card->amount, 2) }}</td>
                    <td>
                        @if($card->status === 'unused')
                            <span class="badge badge-success">未使用</span>
                        @elseif($card->status === 'used')
                            <span class="badge badge-secondary">已使用</span>
                        @else
                            <span class="badge badge-danger">已禁用</span>
                        @endif
                    </td>
                    <td>{{ $card->user->name ?? '-' }}</td>
                    <td>{{ $card->used_at ? $card->used_at->format('Y-m-d H:i') : '-' }}</td>
                    <td>{{ $card->batch_id ?? '-' }}</td>
                    <td>{{ $card->remark ?? '-' }}</td>
                    <td>
                        @if($card->status === 'unused')
                            <form action="{{ route('admin.cards.disable', $card) }}" method="POST" style="display:inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-warning">禁用</button>
                            </form>
                        @elseif($card->status === 'disabled')
                            <form action="{{ route('admin.cards.enable', $card) }}" method="POST" style="display:inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success">启用</button>
                            </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        {{ $cards->links() }}
    </div>
</div>

<!-- 生成卡密弹窗 -->
<div class="modal fade" id="generateModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.cards.generate') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">生成卡密</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>面值金额 (元)</label>
                        <input type="number" name="amount" class="form-control" step="0.01" min="0.01" required placeholder="例如：10.00">
                    </div>
                    <div class="form-group">
                        <label>生成数量</label>
                        <input type="number" name="count" class="form-control" min="1" max="1000" required value="10">
                    </div>
                    <div class="form-group">
                        <label>备注（可选）</label>
                        <input type="text" name="remark" class="form-control" placeholder="例如：618活动">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">取消</button>
                    <button type="submit" class="btn btn-success">生成</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
