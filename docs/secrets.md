# Secrets


```shell
openssl rand -base64 32 > secrets/database_password
```

```shell
bin/console secrets:generate-keys
```

```shell
bin/console secrets:set DATABASE_PASSWORD secrets/database_password
```

```shell
bin/console secrets:list --reveal
```
