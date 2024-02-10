<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240208091050 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE categorias (cod_cat INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(45) NOT NULL, descripcion VARCHAR(200) NOT NULL, PRIMARY KEY(cod_cat)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE pedidos (cod_ped INT AUTO_INCREMENT NOT NULL, fecha DATE NOT NULL, enviado INT NOT NULL, restaurante_id INT NOT NULL, INDEX IDX_6716CCAA38B81E49 (restaurante_id), PRIMARY KEY(cod_ped)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE pedidos_productos (cod_ped_prod INT AUTO_INCREMENT NOT NULL, unidades INT NOT NULL, pedido_id INT NOT NULL, producto_id INT NOT NULL, INDEX IDX_2FA411784854653A (pedido_id), INDEX IDX_2FA411787645698E (producto_id), PRIMARY KEY(cod_ped_prod)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE productos (cod_prod INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(45) NOT NULL, descripcion VARCHAR(90) NOT NULL, peso DOUBLE PRECISION NOT NULL, stock INT NOT NULL, categoria_id INT NOT NULL, INDEX IDX_767490E63397707A (categoria_id), PRIMARY KEY(cod_prod)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE restaurantes (cod_res INT AUTO_INCREMENT NOT NULL, correo VARCHAR(90) NOT NULL, clave VARCHAR(45) NOT NULL, pais VARCHAR(45) NOT NULL, cp INT NOT NULL, ciudad VARCHAR(45) NOT NULL, direccion VARCHAR(200) NOT NULL, PRIMARY KEY(cod_res)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE pedidos ADD CONSTRAINT FK_6716CCAA38B81E49 FOREIGN KEY (restaurante_id) REFERENCES restaurantes (cod_res)');
        $this->addSql('ALTER TABLE pedidos_productos ADD CONSTRAINT FK_2FA411784854653A FOREIGN KEY (pedido_id) REFERENCES pedidos (cod_ped)');
        $this->addSql('ALTER TABLE pedidos_productos ADD CONSTRAINT FK_2FA411787645698E FOREIGN KEY (producto_id) REFERENCES productos (cod_prod)');
        $this->addSql('ALTER TABLE productos ADD CONSTRAINT FK_767490E63397707A FOREIGN KEY (categoria_id) REFERENCES categorias (cod_cat)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pedidos DROP FOREIGN KEY FK_6716CCAA38B81E49');
        $this->addSql('ALTER TABLE pedidos_productos DROP FOREIGN KEY FK_2FA411784854653A');
        $this->addSql('ALTER TABLE pedidos_productos DROP FOREIGN KEY FK_2FA411787645698E');
        $this->addSql('ALTER TABLE productos DROP FOREIGN KEY FK_767490E63397707A');
        $this->addSql('DROP TABLE categorias');
        $this->addSql('DROP TABLE pedidos');
        $this->addSql('DROP TABLE pedidos_productos');
        $this->addSql('DROP TABLE productos');
        $this->addSql('DROP TABLE restaurantes');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
