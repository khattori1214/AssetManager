USE new_training2_0;

-- 1. 配送先テーブル
CREATE TABLE delivery_addresses (
    delivery_address_id INT AUTO_INCREMENT PRIMARY KEY
        COMMENT '配送先ID',
    delivery_address_code VARCHAR(20) NOT NULL UNIQUE
        COMMENT '配送先コード',
    delivery_address_name VARCHAR(100) NOT NULL
        COMMENT '配送先名:支店・営業所・倉庫・物流センター',
    addressee VARCHAR(100) NOT NULL COMMENT '宛名',
    delivery_address_postcode VARCHAR(10) NOT NULL COMMENT '郵便番号',
    delivery_address VARCHAR(100) NOT NULL COMMENT '配送先住所',
    phonenumber VARCHAR(20) NOT NULL COMMENT '配送先電話番号',
    usage_status VARCHAR(100) NOT NULL COMMENT '利用状態',
    created_at TIMESTAMP NOT NULL COMMENT '作成日時',
    updated_at TIMESTAMP NOT NULL COMMENT '更新日時',
    client_id INT NOT NULL COMMENT '顧客ID',

    FOREIGN KEY (client_id)
        REFERENCES clients (client_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='配送先情報';


-- 2. 注文テーブルに配送先関連カラムを追加
-- 既存の注文には情報がないため、追加時点ではNULLを許可
ALTER TABLE orders
    ADD COLUMN delivery_address_id INT NULL
        COMMENT '配送先ID',
    ADD COLUMN order_address_name VARCHAR(100) NULL
        COMMENT '注文時配送先名:支店・営業所・倉庫・物流センター',
    ADD COLUMN order_address_postcode VARCHAR(10) NULL
        COMMENT '注文時郵便番号',
    ADD COLUMN order_address VARCHAR(100) NULL
        COMMENT '注文時配送先住所',
    ADD COLUMN order_addressee VARCHAR(100) NULL
        COMMENT '注文時宛名',
    ADD COLUMN order_phonenumber VARCHAR(20) NULL
        COMMENT '注文時電話番号',

    ADD CONSTRAINT fk_orders_delivery_address
        FOREIGN KEY (delivery_address_id)
        REFERENCES delivery_addresses (delivery_address_id);


-- 3. 部署テーブルに日時カラムを追加
ALTER TABLE departments
    ADD COLUMN created_at TIMESTAMP NULL COMMENT '作成日時',
    ADD COLUMN updated_at TIMESTAMP NULL COMMENT '更新日時';

-- 既存の部署には、今回の補完日時を設定
UPDATE departments
SET created_at = NOW(),
    updated_at = NOW();

-- 値を入れたのでNOT NULLに変更
ALTER TABLE departments
    MODIFY COLUMN created_at TIMESTAMP NOT NULL COMMENT '作成日時',
    MODIFY COLUMN updated_at TIMESTAMP NOT NULL COMMENT '更新日時';


-- 4. 出荷テーブル
CREATE TABLE shipments (
    shipment_id INT AUTO_INCREMENT PRIMARY KEY COMMENT '出荷ID',
    shipment_number VARCHAR(20) NOT NULL UNIQUE COMMENT '出荷番号',
    shipment_day DATE NOT NULL COMMENT '出荷日',
    created_at TIMESTAMP NOT NULL COMMENT '作成日時',
    updated_at TIMESTAMP NOT NULL COMMENT '更新日時',
    order_id INT NOT NULL COMMENT '注文ID',

    FOREIGN KEY (order_id)
        REFERENCES orders (order_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='出荷情報';


-- 5. 出荷明細テーブル
CREATE TABLE shipments_order_details (
    shipments_order_details_id INT AUTO_INCREMENT PRIMARY KEY
        COMMENT '出荷明細ID',
    shipment_id INT NOT NULL COMMENT '出荷ID',
    order_detail_id INT NOT NULL COMMENT '注文明細ID',
    shipment_amount INT NOT NULL COMMENT '出荷数量',

    CONSTRAINT chk_shipment_amount_positive
        CHECK (shipment_amount > 0),

    UNIQUE (shipment_id, order_detail_id),

    FOREIGN KEY (shipment_id)
        REFERENCES shipments (shipment_id),

    FOREIGN KEY (order_detail_id)
        REFERENCES order_details (order_detail_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='出荷明細情報';


-- 6. キャンセルテーブル
CREATE TABLE cancels (
    cancel_id INT AUTO_INCREMENT PRIMARY KEY COMMENT 'キャンセルID',
    cancel_date DATETIME NOT NULL COMMENT 'キャンセル日時',
    cancel_quantity INT NOT NULL COMMENT 'キャンセル数量',
    cancel_reason VARCHAR(200) NOT NULL COMMENT 'キャンセル理由',
    employee_id INT NOT NULL COMMENT '社員ID',
    created_at TIMESTAMP NOT NULL COMMENT '作成日時',
    updated_at TIMESTAMP NOT NULL COMMENT '更新日時',
    order_detail_id INT NOT NULL COMMENT '注文明細ID',

    CONSTRAINT chk_cancel_quantity_positive
        CHECK (cancel_quantity > 0),

    FOREIGN KEY (employee_id)
        REFERENCES employees (employee_id),

    FOREIGN KEY (order_detail_id)
        REFERENCES order_details (order_detail_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='キャンセル情報';
