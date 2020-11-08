<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200921162338 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE carousel (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, id_image_id INTEGER NOT NULL, index_image INTEGER NOT NULL, link VARCHAR(255) DEFAULT NULL, title VARCHAR(50) DEFAULT NULL, is_published BOOLEAN NOT NULL, added_at DATETIME NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1DD747006D7EC9F8 ON carousel (id_image_id)');
        $this->addSql('CREATE TABLE category (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, parent_id_id INTEGER DEFAULT NULL, image_id INTEGER DEFAULT NULL, name VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE INDEX IDX_64C19C1B3750AF4 ON category (parent_id_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_64C19C13DA5256D ON category (image_id)');
        $this->addSql('CREATE TABLE image (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, file VARCHAR(255) NOT NULL, date DATETIME NOT NULL, rank INTEGER DEFAULT NULL)');
        $this->addSql('CREATE TABLE product (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, category_id INTEGER NOT NULL, title VARCHAR(255) NOT NULL, text VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, price INTEGER NOT NULL)');
        $this->addSql('CREATE INDEX IDX_D34A04AD12469DE2 ON product (category_id)');
        $this->addSql('CREATE TABLE product_image (product_id INTEGER NOT NULL, image_id INTEGER NOT NULL, PRIMARY KEY(product_id, image_id))');
        $this->addSql('CREATE INDEX IDX_64617F034584665A ON product_image (product_id)');
        $this->addSql('CREATE INDEX IDX_64617F033DA5256D ON product_image (image_id)');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , password VARCHAR(255) DEFAULT NULL, is_verified BOOLEAN NOT NULL, facebook_id VARCHAR(255) NOT NULL, facebook_access_token VARCHAR(255) DEFAULT NULL, google_id VARCHAR(255) NOT NULL, google_access_token VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX unique_id ON user (email, facebook_id, google_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE carousel');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE image');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE product_image');
        $this->addSql('DROP TABLE user');
    }
}
