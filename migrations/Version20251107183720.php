<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251107183720 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create delivery table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE delivery (id VARCHAR(255) NOT NULL, notification_id VARCHAR(255) NOT NULL, notification_type VARCHAR(255) NOT NULL, communication_channel VARCHAR(255) NOT NULL, notification_data JSON NOT NULL, recipient_customer_identifier VARCHAR(255) NOT NULL, recipient_email VARCHAR(255) NOT NULL, recipient_phone VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN delivery.id IS \'(DC2Type:delivery_id)\'');
        $this->addSql('COMMENT ON COLUMN delivery.notification_id IS \'(DC2Type:notification_id)\'');
        $this->addSql('COMMENT ON COLUMN delivery.notification_type IS \'(DC2Type:notification_type)\'');
        $this->addSql('COMMENT ON COLUMN delivery.communication_channel IS \'(DC2Type:communication_channel)\'');
        $this->addSql('COMMENT ON COLUMN delivery.recipient_email IS \'(DC2Type:email)\'');
        $this->addSql('COMMENT ON COLUMN delivery.recipient_phone IS \'(DC2Type:phone)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE delivery');
    }
}
