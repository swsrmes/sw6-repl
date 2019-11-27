CREATE TABLE custom_entity_blog_comment
(
    id                    BINARY(16) NOT NULL,
    recommendation_id     BINARY(16) DEFAULT NULL,
    custom_entity_blog_id BINARY(16) NOT NULL COMMENT 'custom-entity-element',
    created_at            DATETIME     NOT NULL,
    updated_at            DATETIME     DEFAULT NULL,
    title                 VARCHAR(255) NOT NULL,
    content               LONGTEXT     DEFAULT NULL,
    email                 VARCHAR(255) DEFAULT NULL,
    INDEX                 IDX_A328E7D3D173940B (recommendation_id),
    INDEX                 IDX_A328E7D363C8796E (custom_entity_blog_id),
    PRIMARY KEY (id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = 'custom-entity-element';

CREATE TABLE custom_entity_blog
(
    id            BINARY(16) NOT NULL,
    top_seller_id BINARY(16) NOT NULL,
    author_id     BINARY(16) DEFAULT NULL,
    created_at    DATETIME     NOT NULL,
    updated_at    DATETIME         DEFAULT NULL,
    position      INT              DEFAULT NULL,
    rating        DOUBLE PRECISION DEFAULT NULL,
    title         VARCHAR(255) NOT NULL,
    content       LONGTEXT         DEFAULT NULL,
    display       TINYINT(1) DEFAULT NULL,
    payload       JSON             DEFAULT NULL,
    email         VARCHAR(255)     DEFAULT NULL,
    INDEX         IDX_BEE55DA2F22318A8 (top_seller_id),
    INDEX         IDX_BEE55DA2F675F31B (author_id),
    PRIMARY KEY (id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = 'custom-entity-element';

CREATE TABLE custom_entity_blog_product
(
    custom_entity_blog_id BINARY(16) NOT NULL,
    product_id            BINARY(16) NOT NULL,
    INDEX                 IDX_E416B11263C8796E (custom_entity_blog_id),
    INDEX                 IDX_E416B1124584665A (product_id),
    PRIMARY KEY (custom_entity_blog_id, product_id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = 'custom-entity-element';

ALTER TABLE custom_entity_blog_comment
    ADD CONSTRAINT FK_A328E7D3D173940B FOREIGN KEY (recommendation_id) REFERENCES product (id) ON UPDATE CASCADE ON DELETE SET NULL;

ALTER TABLE custom_entity_blog_comment
    ADD CONSTRAINT fk_ce_custom_entity_blog_comment_custom_entity_blog_id FOREIGN KEY (custom_entity_blog_id) REFERENCES custom_entity_blog (id) ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE custom_entity_blog
    ADD CONSTRAINT FK_BEE55DA2F22318A8 FOREIGN KEY (top_seller_id) REFERENCES product (id) ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE custom_entity_blog
    ADD CONSTRAINT FK_BEE55DA2F675F31B FOREIGN KEY (author_id) REFERENCES user (id) ON UPDATE CASCADE ON DELETE SET NULL;

ALTER TABLE custom_entity_blog_product
    ADD CONSTRAINT FK_E416B11263C8796E FOREIGN KEY (custom_entity_blog_id) REFERENCES custom_entity_blog (id) ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE custom_entity_blog_product
    ADD CONSTRAINT FK_E416B1124584665A FOREIGN KEY (product_id) REFERENCES product (id) ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE category
    ADD CONSTRAINT fk_ce_category_custom_entity_blog_id FOREIGN KEY (custom_entity_blog_id) REFERENCES custom_entity_blog (id) ON UPDATE CASCADE ON DELETE SET NULL;
