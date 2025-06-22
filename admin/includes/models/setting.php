<?php
defined('ABSPATH') || exit;

if (!class_exists('GTM_Setting')) {

    class GTM_Setting
    {
        public string $id;
        public string $label;
        public string $group;
        public string $section;
        public string $type;
        public array $choices;
        public $value;
        public $default_value;
        public ?string $description;
        public ?bool $required;

        public function __construct(
            string $id,
            string $label,
            string $group,
            string $section,
            string $type = 'text',
            ?string $description = null,
            ?string $default_value = null,
            ?array $choices = [],
        ) {
            $this->id            = $id;
            $this->label         = $label;
            $this->group         = $group;
            $this->section       = $section;
            $this->type          = $type;
            $this->description   = $description;
            $this->choices       = $choices;
            $this->default_value = $default_value;
        }

        public function create()
        {
            if ($this->type === 'password') {
                register_setting($this->group, $this->id, [
                    'type' => 'string',
                    'sanitize_callback' => [$this, 'gtm_encrypt_password_callback'],
                    'show_in_rest' => false,
                ]);
            } else {
                register_setting($this->group, $this->id);
            }

            add_settings_field(
                $this->id,
                $this->label,
                [$this, 'render_field'],
                $this->group,
                $this->section
            );
        }

        public function render_field()
        {

            $value = (get_option($this->id)) ? get_option($this->id) : $this->default_value;

            switch ($this->type) {
                case 'text':
                case 'number':
                case 'color':
                    printf(
                        "<input type='%s' name='%s' value='%s' class='regular-text' />",
                        esc_attr($this->type),
                        esc_attr($this->id),
                        esc_attr($value)
                    );
                    break;
                case 'password':
                    printf(
                        "<input type='password' name='%s' value='%s' class='regular-text' />",
                        esc_attr($this->id),
                        esc_attr(GTMAdmin::gtm_decrypt($value))
                    );
                    break;

                case 'checkbox':
                    printf(
                        "<label><input type='checkbox' name='%s' value='yes' %s /> %s</label>",
                        esc_attr($this->id),
                        checked($value, 'yes', false),
                        esc_html($this->description ?? '')
                    );
                    return;

                case 'textarea':
                    printf(
                        "<textarea name='%s' class='large-text' rows='5'>%s</textarea>",
                        esc_attr($this->id),
                        esc_textarea($value)
                    );
                    break;

                case 'select':
                    echo "<select name='" . esc_attr($this->id) . "'>";
                    foreach ($this->choices as $key => $label) {
                        printf(
                            "<option value='%s' %s>%s</option>",
                            esc_attr($key),
                            selected($value, $key, false),
                            esc_html($label)
                        );
                    }
                    echo "</select>";
                    break;
            }

            if ($this->description && $this->type !== 'checkbox') {
                echo "<p class='description'>" . esc_html($this->description) . "</p>";
            }
        }

        function gtm_encrypt_password_callback($raw_value)
        {
            if (empty($raw_value)) {
                return ''; // optionally keep previous value
            }

            // Sanitize and encrypt
            $raw_value = sanitize_text_field($raw_value);
            return GTMAdmin::gtm_encrypt($raw_value);
        }
    }
}
