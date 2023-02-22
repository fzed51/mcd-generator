# MCD generator

**MCD generator** est un script qui permet de générer un MCD pout **Plant UML**

## Utilisation 

### Pour Oracle
```
php mcd-generator.php oci TNSNAME USER PASSWORD
```

### Pour PostgreSQL
```
php mcd-generator.php pgsql HOST DBNAME USER PASSWORD [PORT]
```

### Pour MySql
```
php mcd-generator.php mysql HOST DBNAME USER PASSWORD [PORT]
```

### Pour Sqlite
```
php mcd-generator.php sqlite FILENAME
```