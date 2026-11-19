<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261119100009 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE film (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, synopsis LONGTEXT DEFAULT NULL, duration INT NOT NULL, release_date DATE DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE reservation (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL, status VARCHAR(20) NOT NULL, user_id INT NOT NULL, session_id INT NOT NULL, INDEX IDX_42C84955A76ED395 (user_id), INDEX IDX_42C84955613FECDF (session_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE reservation_seat (reservation_id INT NOT NULL, seat_id INT NOT NULL, INDEX IDX_2B65FB0EB83297E7 (reservation_id), INDEX IDX_2B65FB0EC1DAFE35 (seat_id), PRIMARY KEY (reservation_id, seat_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE seat (id INT AUTO_INCREMENT NOT NULL, room_number INT NOT NULL, row_label VARCHAR(5) NOT NULL, seat_number INT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE session (id INT AUTO_INCREMENT NOT NULL, starts_at DATETIME NOT NULL, room_number INT NOT NULL, price NUMERIC(6, 2) NOT NULL, film_id INT NOT NULL, INDEX IDX_D044D5D4567F5183 (film_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, first_name VARCHAR(100) NOT NULL, last_name VARCHAR(100) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955613FECDF FOREIGN KEY (session_id) REFERENCES session (id)');
        $this->addSql('ALTER TABLE reservation_seat ADD CONSTRAINT FK_2B65FB0EB83297E7 FOREIGN KEY (reservation_id) REFERENCES reservation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reservation_seat ADD CONSTRAINT FK_2B65FB0EC1DAFE35 FOREIGN KEY (seat_id) REFERENCES seat (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE session ADD CONSTRAINT FK_D044D5D4567F5183 FOREIGN KEY (film_id) REFERENCES film (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955A76ED395');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955613FECDF');
        $this->addSql('ALTER TABLE reservation_seat DROP FOREIGN KEY FK_2B65FB0EB83297E7');
        $this->addSql('ALTER TABLE reservation_seat DROP FOREIGN KEY FK_2B65FB0EC1DAFE35');
        $this->addSql('ALTER TABLE session DROP FOREIGN KEY FK_D044D5D4567F5183');
        $this->addSql('DROP TABLE film');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('DROP TABLE reservation_seat');
        $this->addSql('DROP TABLE seat');
        $this->addSql('DROP TABLE session');
        $this->addSql('DROP TABLE user');
    }
}
