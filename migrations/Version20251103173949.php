<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251103173949 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Update notification table columns';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE notification ADD data JSON NOT NULL');
        $this->addSql('ALTER TABLE notification ADD state VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE notification ADD recipient_customer_identifier VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE notification ADD recipient_email VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE notification ADD recipient_phone VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE notification DROP message');
        $this->addSql('ALTER TABLE notification DROP channel');
        $this->addSql('ALTER TABLE notification DROP recipient_identifier');
        $this->addSql('ALTER TABLE notification DROP status');
        $this->addSql('ALTER TABLE notification DROP sent_at');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE notification ADD message TEXT NOT NULL');
        $this->addSql('ALTER TABLE notification ADD channel VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE notification ADD recipient_identifier VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE notification ADD status VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE notification ADD sent_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE notification DROP data');
        $this->addSql('ALTER TABLE notification DROP state');
        $this->addSql('ALTER TABLE notification DROP recipient_customer_identifier');
        $this->addSql('ALTER TABLE notification DROP recipient_email');
        $this->addSql('ALTER TABLE notification DROP recipient_phone');
        $this->addSql('COMMENT ON COLUMN notification.sent_at IS \'(DC2Type:datetime_immutable)\'');
    }
}
