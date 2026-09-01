@extends('layouts.app')

@section('content')

    <div class="content-area">

        <a href="/admin" class="{{ request()->is('admin*') ? 'active' : '' }}">
            <h1>資産登録・在庫管理画面</h1>
        </a>
        <button type="button" onclick="document.getElementById('loanRegisterModal').showModal()">
            貸出資産を登録する
        </button>

        <button type="button" onclick="document.getElementById('consumableRegisterModal').showModal()">
            消耗品を登録する
        </button>
        <h2>貸出資産一覧</h2>

        <table border="1">
            <tr>
                <th>NO.</th>
                <th>資産名</th>
                <th>カテゴリ</th>
                <th>状態</th>
                <th>最大貸出期間</th>
                <th>操作</th>
            </tr>


            @foreach ($loanAssetData as $asset)
                <tr>
                    <td>{{ $asset->asset_id }}</td>
                    <td>{{ $asset->asset_name }}</td>
                    <td>{{ $asset->category_name }}</td>
                    <td>
                        @if ($asset->is_borrowed)
                            貸出中
                        @else
                            利用可能
                        @endif
                    </td>
                    <td>{{ $asset->max_loan_days }}日</td>
                    <td>

                        <button type="button" class="cancel-button" onclick="openDeleteModal('{{ $asset->asset_id }}', '{{ $asset->asset_name }}')">
                            削除する
                        </button>
                    </td>
                </tr>
            @endforeach
        </table>

        <div class="pagination-wrapper">
            {{ $loanAssetData->links('pagination::bootstrap-4') }}
        </div>


        <!-- 消耗品一覧 -->
        <h2>消耗品一覧</h2>

        <table border="1">
            <tr>
                <th>NO.</th>
                <th>資産名</th>
                <th>在庫数</th>
                <th>状態</th>
                <th>操作</th>
            </tr>

            @foreach ($consumableAssetData as $asset)
                <tr>
                    <td>{{ $asset->asset_id }}</td>
                    <td>{{ $asset->asset_name }}</td>
                    <td>{{ $asset->stock }}</td>

                    <td>
                        @if ($asset->stock <= $asset->min_stock)
                            要発注
                        @else
                            在庫あり
                        @endif
                    </td>

                    <td>
                        <button type="button"
                            onclick="document.getElementById('stockModal-{{ $asset->asset_id }}').showModal()">
                            在庫を更新する
                        </button>

                        <button type="button" class="cancel-button" onclick="openDeleteModal('{{ $asset->asset_id }}', '{{ $asset->asset_name }}')">
                            削除する
                        </button>
                    </td>
                </tr>
            @endforeach
        </table>

        <div class="pagination-wrapper">
            {{ $consumableAssetData->links('pagination::bootstrap-4') }}
        </div>

        <!-- 消耗品在庫更新ダイアログ -->
        @foreach ($consumableAssetData as $asset)
            <dialog id="stockModal-{{ $asset->asset_id }}">
                <h2>消耗品の在庫を更新する</h2>

                <form action="/admin/assets/{{ $asset->asset_id }}/stock" method="post">
                    @csrf
                    @method('PATCH')

                    <p>
                        資産名：{{ $asset->asset_name }}（変更不可）
                    </p>

                    <div>
                        <label>在庫数</label>
                        <input type="number" name="stock" value="{{ old('stock', $asset->stock) }}" min="0" required>
                    </div>

                    <div>
                        <label>最低キープ数</label>
                        <input type="number" name="min_stock" value="{{ old('min_stock', $asset->min_stock) }}" min="0"
                            required>
                    </div>

                    <p>単位：{{ $asset->unit }}（変更不可）</p>

                    <p>
                        1回の最大申請数：
                        {{ $asset->max_request_quantity }}（変更不可）
                    </p>

                    <p>
                        月間最大申請回数：
                        {{ $asset->monthly_request_limit }}（変更不可）
                    </p>


                    <button type="submit">更新する</button>

                    <button type="button" class="cancel-button"
                        onclick="document.getElementById('stockModal-{{ $asset->asset_id }}').close()">
                        キャンセル
                    </button>

                </form>
            </dialog>
        @endforeach



        <dialog id="loanRegisterModal">
            <h2>貸出資産を登録する</h2>

            <form action="/admin/assets" method="post">
                @csrf

                <input type="hidden" name="asset_type" value="loan">

                <div>
                    <label>資産名</label>
                    <input type="text" name="asset_name" required>
                </div>

                <div>
                    <label>カテゴリ名</label>
                    <!-- プルダウンに -->
                    <select name="category_id">
                        <option value="1">PC</option>
                        <option value="2">BOOK</option>
                    </select>
                </div>

                <div>
                    <label>単位</label>
                    <select name="unit">
                        <option value="台">台</option>
                        <option value="冊">冊</option>
                        <option value="本">本</option>
                        <option value="個">個</option>
                        <option value="枚">枚</option>
                        <option value="箱">箱</option>
                    </select>
                </div>

                <button type="submit">登録する</button>

                <button type="button" class="cancel-button" onclick="document.getElementById('loanRegisterModal').close()">
                    キャンセル
                </button>
            </form>
        </dialog>

        <dialog id="consumableRegisterModal">
            <h2>消耗品を登録する</h2>

            <form action="/admin/assets" method="post">
                @csrf

                <input type="hidden" name="asset_type" value="consumable">

                <div>
                    <label>資産名</label>
                    <input type="text" name="asset_name" required>
                </div>

                <div>
                    <label>在庫数</label>
                    <input type="number" name="stock" min="0" required>
                </div>

                <div>
                    <label>最低キープ数</label>
                    <input type="number" name="min_stock" min="0" required>
                </div>

                <div>
                    <label>単位</label>
                    <select name="unit">
                        <option value="台">台</option>
                        <option value="冊">冊</option>
                        <option value="本">本</option>
                        <option value="個">個</option>
                        <option value="枚">枚</option>
                        <option value="箱">箱</option>
                    </select>
                </div>

                <div>
                    <label>1回の最大申請数</label>
                    <input type="number" name="max_request_quantity" min="1">
                </div>

                <div>
                    <label>月間最大申請回数</label>
                    <input type="number" name="monthly_request_limit" min="1">
                </div>

                <button type="submit">登録する</button>

                <button type="button" class="cancel-button"
                    onclick="document.getElementById('consumableRegisterModal').close()">
                    キャンセル
                </button>
            </form>
        </dialog>


        <h2>経理連携用CSVファイル</h2>
        <a href="/admin/csv/download">
            経理連携用CSVをダウンロード
        </a>


        <dialog id="deleteModal">
            <h2>貸出資産・消耗品削除</h2>

            <p>
                「<span id="deleteAssetName"></span>」を削除しますか？
            </p>

            <form id="deleteForm" method="post">
                @csrf
                @method('DELETE')

                <button type="submit">
                    削除する
                </button>

                <button type="button" class="cancel-button" onclick="document.getElementById('deleteModal').close()">
                    キャンセル
                </button>

            </form>
        </dialog>


        <script>
            function openDeleteModal(id, name) {
                document.getElementById('deleteAssetName').textContent = name;
                document.getElementById('deleteForm').action = '/admin/assets/' + id;
                document.getElementById('deleteModal').showModal();
            }
        </script>


    </div>
@endsection