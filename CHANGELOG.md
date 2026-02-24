### [8.1.0] - 2026-02-23

#### Added
- Comprehensive test suite (Unit and Feature tests).
- PHPUnit 9.3 and Orchestra Testbench 6 support.
- PHP class literals (`::class`) instead of hardcoded strings for models and controllers.

#### Changed
- Refactored variable names from `snake_case` to `camelCase` for better consistency.
- Standardized imports at the top of PHP files to avoid fully qualified namespaced paths in the code.
- Corrected pivot key order in `belongsToMany` relationships (`Conversation` and `CanConfer` trait).
- Qualified `id` column references in Eloquent queries to resolve ambiguous column errors.
- Explicitly cast `pluck()` results to arrays when used with native PHP functions.
- Backported PHP 7.3 compatibility fixes for the test suite (anonymous functions in `TestCase.php`).
- Optimized `src/Conversation.php` for PHP 7.3 by removing PHP 8.0+ type hints.

### [8.0.0] - 2026-02-22

#### Added
- Support for Laravel 8.0 (`illuminate/support: ^8.0`).
- Support for Pusher 4.0 (`pusher/pusher-php-server: ^4.0`).
- Minimum PHP requirement bumped to `>=7.3` to match Laravel 8.
- Support for Font Awesome 6 icons.

#### Changed
- Modernized JavaScript: replaced `var` with `const`/`let` and improved code style.
- Updated Pusher instantiation and improved JS initialization.
- Moved authentication from external JS file to Blade templates.
- Improved overlay behavior and markup in conversation list.
- Improved `README.md` with accurate branch details and installation steps.

#### Fixed
- Layout of `confer-icon-list`: ensured icons are in a column instead of a row.
- Replaced missing Weixin brand icon with FA6 solid comments icon.
- Implemented delegated event listeners for chat icons.
- Enabled verbose logging for easier troubleshooting.

#### Removed
- Removed `laravelcollective/html` dependency and replaced it with plain HTML in views.

### [Unreleased]

### [7.0.0] - 2026-02-19

#### Added
- Support for Laravel 7.0 (`illuminate/support: ^7.0`).
- Minimum PHP requirement bumped to `>=7.2.5` to match Laravel 7.

#### Fixed
- Typo in `composer.json` metadata: `minimum-stability`.

### Suggestions for Upgrading `tpojka/confer` to Laravel 6

Based on the audit of the `tpojka/confer` submodule, here are the suggested changes to ensure compatibility with Laravel 6.0.

#### 1. Update `submodule/confer/composer.json`
The dependencies need to be bumped to support Laravel 6.0 and the corresponding version of `laravelcollective/html`.

```json
{
    "require": {
        "php": ">=7.2.0",
        "illuminate/support": "^6.0",
        "laravelcollective/html": "^6.0",
        "pusher/pusher-php-server": "^3.0"
    }
}
```
*Note: PHP version requirement should be at least `7.2.0` as per Laravel 6 requirements.*

#### 2. Replace `lists()` with `pluck()`
The `lists()` method was deprecated in Laravel 5.2 and removed in 5.3, but it was still present in some older versions of the `tpojka/confer` codebase. In Laravel 6, you must use `pluck()` instead.

**Affected Files:**

*   **`submodule/confer/src/Conversation.php`**
    ```php
    // Line 57
    $current_participants = $this->participants()->pluck('id');

    // Line 69
    $current_participants = $this->participants()->pluck('id');

    // Line 78
    $this->participants()->sync(array_merge($this->participants()->pluck('id')->toArray(), $users));
    ```

*   **`submodule/confer/src/Commands/ParticipantsWereAdded.php`**
    ```php
    // Line 57
    $users = $this->conversation_was_created ? confer_make_list($this->conversation->participants()->ignoreMe()->pluck('name')->toArray()) : confer_make_list(User::whereIn('id', $this->users)->pluck('name')->toArray());
    ```

*   **`submodule/confer/src/views/conversation.blade.php`**
    ```blade
    {{-- Line 6 --}}
    Conversation between {{ confer_make_list($conversation->participants->pluck('name')->toArray()) }}.
    ```
    *Note: When calling `pluck()` on an Eloquent collection in a context where an array is expected (like `confer_make_list`), appending `->toArray()` is recommended for clarity.*

#### 3. Update Service Provider for Route Model Binding
While `Route::model` still works in Laravel 6, it is often recommended to move route definitions into a dedicated `routes/web.php` or `routes/api.php` file if they are part of a package's standard behavior, and use `Route::bind` if complex logic is needed. However, for a simple upgrade, keeping `Route::model` is acceptable.

#### 4. Handling Carbon Changes
Laravel 6 uses Carbon 2. Ensure that any date formatting in views (like `diffForHumans()`) remains consistent with your expectations, as Carbon 2 introduced some minor behavior changes in localization.

#### 5. Future Considerations (Guideline Alignment)
As you plan to move towards Tailwind and Vue:
*   **Tailwind:** The current views use some Bootstrap-like classes (e.g., in `conversation.blade.php`). You can start by adding a `confer-tailwind.css` or similar to begin the transition.
*   **Laravel Collective:** Since you plan to replace `laravelcollective/html`, consider moving towards plain HTML or Vue components for forms in `src/views/conversation.blade.php` and `src/views/invite.blade.php`.

### Summary of Actions
1.  Bump versions in `composer.json`.
2.  Search and replace `->lists(` with `->pluck(` across the entire `submodule/confer/src` directory.
3.  Test the package functionality with a Laravel 6.x installation.
