<?php

class Migrations_Migration804 extends Shopware\Components\Migrations\AbstractMigration
{
    public function up($modus)
    {
        $sql = <<<'EOD'
SET @help_parent = (SELECT id FROM s_core_config_forms WHERE name='Frontend' LIMIT 1);
EOD;
        $this->addSql($sql);

        $sql = <<<'EOD'
INSERT IGNORE INTO `s_core_config_forms` (`parent_id`, `name`, `label`, `description`, `position`) VALUES
(@help_parent , 'Captcha', 'Captcha', NULL, 0);
EOD;
        $this->addSql($sql);

        $sql = <<<'EOD'
SET @parent = (SELECT id FROM s_core_config_forms WHERE name = 'Captcha' AND parent_id=@help_parent LIMIT 1);
EOD;
        $this->addSql($sql);

        $sql = <<<'EOD'
INSERT IGNORE INTO `s_core_config_elements`
(`form_id`, `name`, `value`, `label`, `description`, `type`, `required`, `position`, `scope`, `options`)
VALUES
(@parent, 'captchaMethod', 's:7:"default";', 'Captcha Methode', 'Wählen Sie hier eine Methode aus, wie die Formulare gegen Spam-Bots geschützt werden sollen', 'combo', 1, 0, 1, 'a:5:{s:8:"editable";b:0;s:10:"valueField";s:2:"id";s:12:"displayField";s:11:"displayname";s:13:"triggerAction";s:3:"all";s:5:"store";s:12:"base.Captcha";}'),
(@parent, 'noCaptchaAfterLogin', 'b:0;', 'Nach Login ausblenden', 'Nach dem Login können Kunden Formulare ohne Captcha-Überprüfung absenden.', 'checkbox', 0, 1, 1, '');
EOD;
        $this->addSql($sql);

        $sql = <<<'EOD'
UPDATE s_core_config_elements SET form_id=@parent WHERE name='captchaColor';
EOD;
        $this->addSql($sql);

        $sql = <<<'EOD'
SET @captchaMethod = (SELECT id FROM s_core_config_elements WHERE name = 'captchaMethod' LIMIT 1);
EOD;
        $this->addSql($sql);

        $sql = <<<'EOD'
SET @noCaptchaAfterLogin = (SELECT id FROM s_core_config_elements WHERE name = 'noCaptchaAfterLogin' LIMIT 1);
EOD;
        $this->addSql($sql);

        $sql = <<<'EOD'
INSERT IGNORE INTO `s_core_config_element_translations` (`element_id`, `locale_id`, `label`, `description`)
VALUES
(@captchaMethod, '2', 'Captcha Method', 'Choose the method to protect the forms against spam bots.'),
(@noCaptchaAfterLogin, '2', 'Disable after login', 'If set to yes, captchas are disabled for logged in customers');
EOD;
        $this->addSql($sql);
    }
}
