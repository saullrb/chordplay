# ChordPlay

App to display lyrics with synced chords, for playing or learning songs.

## Requirements

- PHP 8.2
- Composer
- Node.js 22
- NPM

## Installation

1. Clone the repository:

    ```sh
    git clone https://github.com/saullbrandao/chordplay.git
    cd chordplay
    ```

2. Install PHP dependencies:

    ```sh
    composer install
    ```

3. Install Node.js dependencies:

    ```sh
    npm install
    ```

4. Copy the example environment file and generate an application key:

    ```sh
    cp .env.example .env
    php artisan key:generate
    ```

    - If you want to use Dusk tests, copy the example environment file and generate an application key:

        ```sh
        cp .env.dusk.example .env.dusk.local
        php artisan key:generate --env=dusk.local
        ```

5. Set up Google OAuth credentials (required for authenticated features):

    > Note: Viewing artists, songs, and using search does **not** require login. Authentication is only needed for restricted features (e.g., adding/editing content).

    - Go to <https://console.cloud.google.com/>
    - Create a new project or select an existing one.

    - Navigate to APIs & Services > Credentials.
    - Click Create Credentials and select OAuth client ID.

    - Choose Web application as the application type.

    - Add <http://localhost:8000/auth/google/callback> to the Authorized redirect URIs.

    - Click Create and copy your Client ID and Client Secret.

    - Create OAuth credentials

    - Add `http://localhost:8000/auth/google/callback` as an authorized redirect URI

    - Set the following in `.env`:

        ```env
        GOOGLE_CLIENT_ID=
        GOOGLE_CLIENT_SECRET=
        ```

6. Create the SQLite database and run migrations:

    ```sh
    php artisan migrate
    ```

7. (Optional) Seed the database with sample data:

    ```sh
    php artisan db:seed
    ```

8. Then run the application:

    ```sh
    composer dev
    ```
