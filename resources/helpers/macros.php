<?php

/*
  |--------------------------------------------------------------------------
  | Form Macro
  |--------------------------------------------------------------------------
  |
  | Macro for admin side bar menu
  |
 */

use Illuminate\Support\Facades\Blade;

Html::macro('sidebarOption', function($id, $name, $url, $permission = null, $icon = null, $attributes = array(), $label = null, $iconImg = null) {

    $name = __($name); //translate

    $icon = is_null($icon) ? 'fas fa-angle-right' : $icon;

    $labelTamplate = null;

    if(is_null($permission) || Auth::user()->ability(Config::get('permissions.role.admin'), $permission)) {

        $template = "<li id='sidebar-%s'><a href='%s'" . Html::attributes($attributes) . "><i class='%s'></i> <span>%s</span>%s</a></li>";
        if($iconImg) {
            $img = asset('assets/img/default/'.$iconImg);
            $template = "<li id='sidebar-%s'><a href='%s'" . Html::attributes($attributes) . "><i class='%s'></i> <span>%s</span>%s</a></li>";
        }

        if (!is_null($label)) {
            $labelTamplate = "<span class='label label-%s pull-right'>%s</span>";
            $labelTamplate = sprintf($labelTamplate, $label['class'], $label['value']);
        }

        return sprintf($template, $id, $url, $icon, $name, $labelTamplate);
    }
});

Html::macro('sidebarOptionREST', function($id, $name, $icon = null, $attributes = array(), $label = null) {
    $route = 'admin.' . $id . '.index';
    $name = __($name);
    return Html::sidebarOption($id, $name, route($route), $id, $icon, $attributes, $label);
});

Html::macro('sidebarTreeOpen', function($id, $name, $icon = null) {

    $icon = is_null($icon) ? 'fas fa-circle-o' : $icon;

    $template = "<li class='treeview' id='sidebar-%s'>
        <a href='#'><i class='fas fa-angle-down pull-right'></i> <i class='%s'></i> <span>%s</span> </a>
        <ul class='treeview-menu'>";

    return sprintf($template, $id, $icon, $name);
});

Html::macro('sidebarTreeClose', function() {
    return "</ul></li>";
});


Form::macro('selectWithData', function($name, $list = [], $selected = null, $options = array())
{

    $selected = $this->getValueAttribute($name, $selected);

    $options['id'] = $this->getIdAttribute($name, $options);

    if ( ! isset($options['name'])) $options['name'] = $name;

    $html = [];

    foreach ($list as $list_el)
    {
        $isSelected = $this->getSelectedValue($list_el['value'], $selected);

        $option_attr = $list_el + ['selected' => $isSelected]; //['value' => e($list_el['value']), , 'class' => $list_el['class']];

        $html[] = '<option'.$this->html->attributes($option_attr).'>'.e($list_el['display']).'</option>';
    }

    $options = $this->html->attributes($options);

    $list = implode('', $html);

    return "<select{$options}>{$list}</select>";
});

Form::macro('selectMultiple', function($name, $list = [], $selected = null, $options = array())
{
    // When building a select box the "value" attribute is really the selected one
    // so we will use that when checking the model or session for a value which
    // should provide a convenient method of re-populating the forms on post.

    $selected = $this->getValueAttribute($name, $selected);

    if (!is_array($selected) && strpos($selected, ',') !== false) {
        $selected = explode(',', $selected);
        $selected = array_map('intval', $selected);
    }

    $options['id'] = $this->getIdAttribute($name, $options);

    if (! isset($options['name'])) {
        $options['name'] = $name;
    }

    // We will simply loop through the options and build an HTML value for each of
    // them until we have an array of HTML declarations. Then we will join them
    // all together into one single HTML element that can be put on the form.
    $html = [];

    if (isset($options['placeholder'])) {
        $html[] = $this->placeholderOption($options['placeholder'], $selected);
        unset($options['placeholder']);
    }

    foreach ($list as $value => $display) {
        $html[] = $this->getSelectOption($display, $value, $selected);
    }

    // Once we have all of this HTML, we can join this into a single element after
    // formatting the attributes into an HTML "attributes" string, then we will
    // build out a final select statement, which will contain all the values.
    $options = $this->html->attributes($options);

    $list = implode('', $html);

    return $this->toHtmlString("<select{$options} multiple='multiple'>{$list}</select>");
});



Html::macro('localesOverview', function($model, $name) {

    $html = '';
    $locales = app_locales();

    if(count($locales) > 1) {

        $html = '<br/>';

        foreach($locales as $key => $locale) {

            $title = '';
            $class = 'filter-grayscale';

            if(isset($model->translate($key)->{$name}) && !empty($model->translate($key)->{$name})) {
                $title = $model->translate($key)->{$name};
                $class = '';
            }

            $html.= '<span class="'. $class .' m-r-2"><i class="flag-icon flag-icon-'. $key.'" data-toggle="tooltip" title="'.strtoupper($key).': '.$title.'"></i></span>';
        }
    }

    return $html;
});



Form::macro('textTrans', function($name, $value = null, $options = array())
{
    $fallbackLocale = config('app.fallback_locale');
    $locales = app_locales();
    $appLocale = App::getLocale();

    if(count($locales) <= 1) {
        return Form::text($fallbackLocale.'['.$name.']', $this->model->getTranslation($appLocale)->{$name}, $options);
    }

    $html = '<div class="input-group">';
    $countryList = '';
    $options+= ['data-input-locale' => $fallbackLocale];

    foreach($locales as $key => $locale) {

        //if($key != $fallbackLocale) {
        $options['style'] = 'display:none';
        if($key == $appLocale) {
            $options['style'] = '';
        }

        $options['data-input-locale'] = $key;

        $class = 'text-muted';
        if (isset($this->model)) {
            $value = null;

            if(isset($this->model->getTranslation($key)->{$name}) && !empty($this->model->getTranslation($key)->{$name})) {
                $value = $this->model->getTranslation($key)->{$name};
                $class = 'text-green';
            }
        }

        $html.= Form::text($key.'['.$name.']', $value, $options);

        $countryList.= '<li>'
            . '<a href="#" data-locale-target="'.$key.'">'
            . '<i class="'.$class.' fa fa-circle" style="font-size: 75%"></i>'
            . '<i class="flag-icon flag-icon-'.$key.'"></i> '.$locale
            . '</a>'
            . '</li>';
    }

    $html.= '<div class="input-group-btn" data-toggle="change-locale">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="text-uppercase locale-key">'.App::getLocale().'</span> <span class="caret"></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-right">'.$countryList.'</ul>
            </div>';

    $html.= '</div>';

    return $html;

});

Form::macro('textareaTrans', function($name, $value = null, $options = array())
{
    $fallbackLocale = config('app.fallback_locale');
    $locales = app_locales();
    $appLocale = App::getLocale();

    if(count($locales) <= 1) {
        return Form::textarea($fallbackLocale.'['.$name.']',  $this->model->getTranslation($appLocale)->{$name}, $options);
    }

    $html = '<div class="input-group">';
    $countryList = '';
    $options+= ['data-input-locale' => $fallbackLocale];

    foreach($locales as $key => $locale) {

        if($key != $fallbackLocale) {
            $options+=['style' => 'display:none'];
        }
        $options['data-input-locale'] = $key;

        $class = 'text-muted';
        if (isset($this->model)) {
            $value = null;

            if(isset($this->model->getTranslation($key)->{$name}) && !empty($this->model->getTranslation($key)->{$name})) {
                $value = $this->model->getTranslation($key)->{$name};
                $class = 'text-green';
            }
        }

        $html.= Form::textarea($key.'['.$name.']', $value, $options);

        $countryList.= '<li>'
            . '<a href="#" data-locale-target="'.$key.'">'
            . '<i class="'.$class.' fa fa-circle" style="font-size: 75%"></i>'
            . '<i class="flag-icon flag-icon-'.$key.'"></i> '.$locale
            . '</a>'
            . '</li>';
    }

    $html.= '<div class="input-group-btn" data-toggle="change-locale">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="text-uppercase locale-key">'.App::getLocale().'</span> <span class="caret"></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-right">'.$countryList.'</ul>
            </div>';

    $html.= '</div>';

    return $html;

});


Form::macro('checkboxTrans', function($name, $value = null, $checked = false, $options = array())
{
    $fallbackLocale = config('app.fallback_locale');
    $locales = app_locales();
    $appLocale = App::getLocale();


    if(count($locales) <= 1) {
        $checked = $this->model->getTranslation($appLocale) ? $this->model->getTranslation($appLocale)->{$name} : $checked;
        return Form::checkbox($fallbackLocale.'['.$name.']', $this->model->getTranslation($appLocale)->{$name}, $checked, $options);
    }

    $options+= ['data-input-locale' => $fallbackLocale];
    $html = '';
    foreach($locales as $key => $locale) {

        $html.='<span ';

        if($key != $fallbackLocale) {
            $html.=' style="display:none" ';
        }

        $html.='data-input-locale="'.$key.'">';

        if (isset($this->model)) {
            $checked = $this->model->getTranslation($key) ? $this->model->getTranslation($key)->{$name} : $checked;
        }

        $html.= Form::checkbox($key.'['.$name.']', $value, $checked, $options);
        $html.='</span>';
    }

    return $html;
});


\Illuminate\Database\Query\Builder::macro('constructFullSql', function () {
    $sql = str_replace(['%', '?'], ['%%', '%s'], $this->toSql());

    $handledBindings = array_map(function ($binding) {
        if (is_numeric($binding)) {
            return $binding;
        }

        $value = str_replace(['\\', "'"], ['\\\\', "\'"], $binding);

        return "'{$value}'";
    }, $this->getConnection()->prepareBindings($this->getBindings()));

    $fullSql = vsprintf($sql, $handledBindings);

    return $fullSql;
});

\Illuminate\Database\Query\Builder::macro('fullSql', function () {
    dd($this->constructFullSql());
});

Blade::directive('trans', function ($string) {
    return "<?php echo __($string) ?>";
});
