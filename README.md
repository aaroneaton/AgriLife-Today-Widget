# Texas A&M AgriLife Today Widget
Creates a widget to display stories from today.agrilife.org.

### Note
This code originally lived in the AgriFlex theme. We took it out for portability. And because we're awesome.

## Overriding these styles in your own theme
To create your own CSS for this widget, just add the following code to your `functions.php`:

`remove_action( 'wp_enqueue_scripts', 'agrilife_today_load_styles' );
