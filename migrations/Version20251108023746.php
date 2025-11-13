<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251108023746 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add status, scheduled_at and completed_at columns to delivery table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE delivery ADD status VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE delivery ADD scheduled_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE delivery ADD completed_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN delivery.status IS \'(DC2Type:delivery_status)\'');
        $this->addSql('COMMENT ON COLUMN delivery.scheduled_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN delivery.completed_at IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE delivery DROP status');
        $this->addSql('ALTER TABLE delivery DROP scheduled_at');
        $this->addSql('ALTER TABLE delivery DROP completed_at');
    }
}
