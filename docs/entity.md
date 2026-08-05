bin/console make:entity Country

bin/console make:migration

composer require --dev orm-fixtures

bin/console doctrine:fixtures:load

bin/console doctrine:schema:validate -v
