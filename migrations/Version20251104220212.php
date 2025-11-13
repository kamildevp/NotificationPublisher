<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251104220212 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Added custom types comments on notification table columns';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('COMMENT ON COLUMN notification.id IS \'(DC2Type:notification_id)\'');
        $this->addSql('COMMENT ON COLUMN notification.type IS \'(DC2Type:notification_type)\'');
        $this->addSql('COMMENT ON COLUMN notification.state IS \'(DC2Type:notification_state)\'');
        $this->addSql('COMMENT ON COLUMN notification.recipient_email IS \'(DC2Type:email)\'');
        $this->addSql('COMMENT ON COLUMN notification.recipient_phone IS \'(DC2Type:phone)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('COMMENT ON COLUMN notification.id IS NULL');
        $this->addSql('COMMENT ON COLUMN notification.type IS NULL');
        $this->addSql('COMMENT ON COLUMN notification.state IS NULL');
        $this->addSql('COMMENT ON COLUMN notification.recipient_email IS NULL');
        $this->addSql('COMMENT ON COLUMN notification.recipient_phone IS NULL');
    }
}
