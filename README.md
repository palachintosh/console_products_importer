
# Console Product Importer

This Symfony console application lets you import products from CSV files directly into a MySQL database.

---

## Installation

1. **Clone the repository** and go to the project root:

   ```bash
   git clone git@github.com:palachintosh/console_products_importer.git
   cd console_products_importer
   ```

2. **Install dependencies** with Composer:

   ```bash
   composer install
   ```

3. **Configure environment files** (`.env`, `.env.local`, etc.).

4. **Set the database connection**  
   In `.env.local`, edit:
   
   DATABASE_URL="mysql://db_user:db_pass@127.0.0.1:3306/your_database"

6. **Create the database and run migrations**:

   php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate

---

## Example Usage

### Full import

php bin/console importer:products_importer --path="/home/<user>/products.csv"

### Dry-run (no DB changes)

php bin/console importer:products_importer --path="/home/<user>/products.csv" --test

> **Dry-run mode** executes the entire import workflow without touching the database.  
> Use it to validate your data before inserting.
