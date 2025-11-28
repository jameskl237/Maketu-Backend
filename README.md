# E-commerce-base

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/your-username/E-commerce-base-backend.git
   cd E-commerce-base-backend
   ```

2. Install dependencies:
   ```bash
   composer install
   ```

3. Create your environment file:
   ```bash
   cp .env.example .env
   ```
   Then, configure your database and other settings in the `.env` file.

4. Generate your application key:
   ```bash
   php artisan key:generate
   ```

5. Run the database migrations:
   ```bash
   php artisan migrate
   ```

6. Create the symbolic link to make your storage public:
   ```bash
   php artisan storage:link
   ```

7. (Optional) Seed the database with some data:
   ```bash
   php artisan db:seed
   ```

Now your application should be up and running.