<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180327190919 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE company_groups (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, type SMALLINT NOT NULL, UNIQUE INDEX UNIQ_CDF25D055E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE companies_groups_rel (company_id VARCHAR(255) NOT NULL, group_id INT NOT NULL, INDEX IDX_112EFE66979B1AD6 (company_id), UNIQUE INDEX UNIQ_112EFE66FE54D947 (group_id), PRIMARY KEY(company_id, group_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE companies_groups_rel ADD CONSTRAINT FK_112EFE66979B1AD6 FOREIGN KEY (company_id) REFERENCES companies (market_id)');
        $this->addSql('ALTER TABLE companies_groups_rel ADD CONSTRAINT FK_112EFE66FE54D947 FOREIGN KEY (group_id) REFERENCES company_groups (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE companies_groups_rel DROP FOREIGN KEY FK_112EFE66FE54D947');
        $this->addSql('DROP TABLE company_groups');
        $this->addSql('DROP TABLE companies_groups_rel');
    }
}
