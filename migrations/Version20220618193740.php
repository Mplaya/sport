<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220618193740 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE events (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE match_events (id INT AUTO_INCREMENT NOT NULL, match_id INT NOT NULL, team_id INT NOT NULL, player INT DEFAULT NULL, event_id INT NOT NULL, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE matches (id INT AUTO_INCREMENT NOT NULL, home_team_id INT NOT NULL, away_team_id INT NOT NULL, status INT NOT NULL, match_date DATETIME DEFAULT NULL, home_team_score INT NOT NULL, away_team_score INT NOT NULL, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE teams (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('INSERT INTO teams (`name`) VALUES (\'Argentina\')');
        $this->addSql('INSERT INTO teams (`name`) VALUES (\'Australia\')');
        $this->addSql('INSERT INTO teams (`name`) VALUES (\'Brazil\')');
        $this->addSql('INSERT INTO teams (`name`) VALUES (\'Canada\')');
        $this->addSql('INSERT INTO teams (`name`) VALUES (\'France\')');
        $this->addSql('INSERT INTO teams (`name`) VALUES (\'Germany\')');
        $this->addSql('INSERT INTO teams (`name`) VALUES (\'Italy\')');
        $this->addSql('INSERT INTO teams (`name`) VALUES (\'Mexico\')');
        $this->addSql('INSERT INTO teams (`name`) VALUES (\'Spain\')');
        $this->addSql('INSERT INTO teams (`name`) VALUES (\'Uruguay\')');

        $this->addSql('INSERT INTO events (`id`,`name`) VALUES (1,\'Home Team Goal\')');
        $this->addSql('INSERT INTO events (`id`,`name`) VALUES (50,\'Away Team Goal\')');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE events');
        $this->addSql('DROP TABLE match_events');
        $this->addSql('DROP TABLE matches');
        $this->addSql('DROP TABLE teams');
    }
}
