<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200128201834 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE reports DROP FOREIGN KEY FK_F11FA7456528D2EF');
        $this->addSql('ALTER TABLE reports_user DROP FOREIGN KEY FK_8AC464667C5EAD31');
        $this->addSql('CREATE TABLE report (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, ad_id INT DEFAULT NULL, comment_id INT DEFAULT NULL, created_at DATETIME NOT NULL, INDEX IDX_C42F7784F675F31B (author_id), INDEX IDX_C42F77844F34D596 (ad_id), INDEX IDX_C42F7784F8697D13 (comment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_C42F7784F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_C42F77844F34D596 FOREIGN KEY (ad_id) REFERENCES ad (id)');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_C42F7784F8697D13 FOREIGN KEY (comment_id) REFERENCES comment (id)');
        $this->addSql('DROP TABLE report_ad');
        $this->addSql('DROP TABLE reports');
        $this->addSql('DROP TABLE reports_user');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE report_ad (id INT AUTO_INCREMENT NOT NULL, ad_id INT NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_830B2EC84F34D596 (ad_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE reports (id INT AUTO_INCREMENT NOT NULL, report_ad_id INT DEFAULT NULL, created_at DATETIME NOT NULL, INDEX IDX_F11FA7456528D2EF (report_ad_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE reports_user (reports_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_8AC464667C5EAD31 (reports_id), INDEX IDX_8AC46466A76ED395 (user_id), PRIMARY KEY(reports_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE report_ad ADD CONSTRAINT FK_830B2EC84F34D596 FOREIGN KEY (ad_id) REFERENCES ad (id)');
        $this->addSql('ALTER TABLE reports ADD CONSTRAINT FK_F11FA7456528D2EF FOREIGN KEY (report_ad_id) REFERENCES report_ad (id)');
        $this->addSql('ALTER TABLE reports_user ADD CONSTRAINT FK_8AC464667C5EAD31 FOREIGN KEY (reports_id) REFERENCES reports (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reports_user ADD CONSTRAINT FK_8AC46466A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE report');
    }
}
