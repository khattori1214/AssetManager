# コミットメッセージの書き方

このプロジェクトでは、コミットメッセージに Conventional Commits 風のプレフィックスを付け、必要に応じて GitHub Issue 番号を併記します。

## 基本形

```text
type: 変更内容

Issueキーワード #Issue番号
```

例:

```text
fix: 返却処理の二重実行を防止

Fixes #31
```

## プレフィックス

| プレフィックス | 使う場面 |
| --- | --- |
| `feat:` | 新機能の追加 |
| `fix:` | 不具合修正 |
| `refactor:` | 振る舞いを変えない内部整理 |
| `style:` | 画面の見た目、レイアウト、フォーマット調整 |
| `test:` | テストの追加・修正 |
| `docs:` | ドキュメントの追加・修正 |
| `chore:` | 設定、依存関係、雑務的な変更 |
| `ci:` | GitHub Actions など CI 設定の変更 |

## Issue番号の書き方

Issueを完了させるコミットでは、本文の最後に `Closes`、`Fixes`、`Resolves` のいずれかを書きます。

```text
feat: 資産一覧に検索条件を追加

Closes #12
```

```text
fix: 返却済み資産を再返却できないように修正

Fixes #31
```

Issueに関連するが、そのコミットだけでは完了しない場合は `Refs` を使います。

```text
refactor: 資産取得処理の責務を整理

Refs #36
```

## 推奨ルール

| コミット種別 | Issueキーワード |
| --- | --- |
| `feat:` | `Closes #番号` |
| `fix:` | `Fixes #番号` |
| `refactor:` | `Refs #番号` |
| `style:` | `Refs #番号` |
| `test:` | `Refs #番号` |
| `docs:` | `Refs #番号` |

ただし、`refactor:` や `docs:` でもIssueを完全に完了させる場合は `Closes #番号` を使って構いません。

## 1行で書く場合

小さな変更では、Issue番号を件名の最後に書いても構いません。

```text
fix: 返却処理の二重実行を防止 #31
```

ただし、Issueを自動で閉じたい場合は本文に `Fixes #31` や `Closes #31` を書く形を優先します。
