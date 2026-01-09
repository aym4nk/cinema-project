<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260104133000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add trailer_filename and image_filename to film';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE film ADD trailer_filename VARCHAR(255) DEFAULT NULL, ADD image_filename VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE film DROP trailer_filename, DROP image_filename');
    }
}
