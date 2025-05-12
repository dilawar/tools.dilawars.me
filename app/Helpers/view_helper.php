<?php

use App\Data\AdminActionName;
use Assert\Assert;
use CodeIgniter\I18n\Time;

/**
 * Build a form. Caller should wrap it inside <form> ...  </form>.
 */
function arrayToFormInner(
    array $formData,
    bool $all_readonly = false,
    string|array|null $submit = null,
    array $extra = []
): string {
    $form = [];

    foreach ($formData as $name => $attr) {
        $id = $name;
        $label = nameToLabel($name);

        $form[] = "<div class='form-group row'>";
        $form[] = "<label for='{$id}' class='col-form-label col-3'>{$label}</label>";

        $type = $attr['type'] ?? 'text';

        if ($all_readonly) {
            $attr['readonly'] = $all_readonly;
        }

        $widthClass = 'col-8';
        if ('select' !== $type) {
            $form[] = arrayToInput($name, id: $id, attr: $attr, divClass: $widthClass);
        } else {
            $form[] = arrayToSelect($name, attr: $attr, divClass: $widthClass);
        }

        $form[] = '</div>';
    }

    if (is_string($submit)) {
        $submit = [
            'name' => 'submit',
            'value' => $submit,
        ];
    }

    if ($submit) {
        $submitAttrs = $extra['submit'] ?? [];
        $submitBtnClasses = 'col-4 m-3 ' . ($submitAttrs['class'] ?? 'btn btn-small btn-primary');

        $form[] = '<div class="row justify-content-end">';
        $form[] = form_submit(
            data: $submit['name'],
            value: $submit['value'],
            extra: [
                'class' => $submitBtnClasses,
            ],
        );
        $form[] = '</div>';
    }

    return implode(' ', $form);
}

function arrayToSelect(string $name, array $attr, string $divClass = ''): string
{
    $type = $attr['type'] ?? 'text';

    $value = $attr['value'] ?? '';
    Assert::that($value)->string();

    $extra = ($attr['readonly'] ?? false) ? 'disabled' : '';

    $id = SELECTIZE_ID_PREFIX . "_$name";
    $form = ['<div class="' . $divClass . '">'];
    $form[] = "<select id='{$id}' {$extra} class='form-control' name='{$name}' type='{$type}' value='{$value}'>";

    $form[] = '<option value=""> -- Please Select -- </option>';
    foreach ($attr['options'] as $id => $option) {
        // The option can be a simple string or a array of two values. If it is
        // an array of two values, first one is the value and second is the
        // label.
        $_value = $option;
        $label = $option;
        if (is_array($option)) {
            $_value = $option[0];
            $label = $option[1];
        }

        $selected = $value === $_value ? 'selected' : '';

        Assert::that($_value)->string();
        Assert::that($label)->string();

        $form[] = "<option {$selected} value='{$_value}'>{$label}</option>";
    }
    $form[] = '</select>';
    $form[] = '</div>';

    return implode(' ', $form);
}

function arrayToInput(string $name, string $id, array $attr, string $divClass = ''): string
{
    $type = $attr['type'] ?? 'text';
    $value = $attr['value'] ?? '';
    $extra = ($attr['readonly'] ?? false) ? 'readonly' : '';

    $form = ['<div class="' . $divClass . '">'];
    $form[] = "<input id='{$id}' name='{$name}' class='form-control' {$extra} type='{$type}' value='{$value}' />";
    $form[] = '</div>';

    return implode(' ', $form);
}

/**
 * Render a query form.
 *
 * @param string  $route   route to open when button is clicked
 * @param string  $referer when query results are shown, this value is used to
 *                         redirect the user back to query page
 * @param ?string $label   shown as placeholder inside input element
 */
function queryForm(string $route, string $referer, ?string $label = null): string
{
    Assert::that($referer)->string()->url();

    $html = [];
    $html[] = form_open($route, hidden: [
        'referer' => $referer,
    ]);
    $html[] = "<div class='d-flex form-group row'>";
    $html[] = "<div class='col-8 col-form-control'>";
    $html[] = form_input(
        [
            'name' => 'query',
            'maxlength' => 40,
            'minlength' => 3,
            'placeholder' => $label ?? 'Query',
            'class' => 'form-control',
        ]
    );
    $html[] = '</div>';

    $html[] = "<div class='col-4'>";
    $html[] = form_submit('submit', 'Query', extra: [
        'class' => 'btn btn-info',
    ]);
    $html[] = '</div>';

    $html[] = '</div>';
    $html[] = '</form>';

    return implode(' ', $html);
}

function renderDocumentUploadFormInner(string $submit = 'Upload File'): string
{
    $form = [];

    $form[] = '<div class="row">';
    $form[] = "<div class='col-8'>";
    $form[] = form_upload('patient_document', extra: [
        'class' => 'form-control',
    ]);
    $form[] = '</div>';

    $form[] = "<div class='col-4'>";
    $form[] = form_submit('submit', $submit, extra: [
        'class' => 'btn btn-secondary',
    ]);
    $form[] = '</div>';
    $form[] = '</div>'; // row ends.

    return implode(' ', $form);
}

/**
 * Convert id to label.
 */
function idToLabel(string $id): string
{
    return ucwords(str_replace('_', ' ', $id));
}

function goToPageLink(string $url, string $label): string
{
    $label = ucwords($label);

    return <<<EOD
        <div class="d-flex justify-content-center mt-4">
            <a href='{$url}' class='float-right'>{$label}</a>
        </div>
        EOD;
}

function renderIssueList(array $issues): string
{
    $html = [];
    $html[] = '<ul>';
    foreach ($issues as $issue) {
        $html[] = "<li> {$issue} </li>";
    }
    $html[] = '</ul>';

    return implode(' ', $html);
}

function locationLabel(array $location): string
{
    $result = $location['name'];
    if ($dept = $location['department']) {
        $result .= " ({$dept})";
    }
    if ($addr = $location['address']) {
        $result .= ", {$addr}";
    }

    return ucwords($result);
}

function renderDocumentDeleteForm(string $action, array $document, string $anchor): string
{
    Assert::that($anchor)->startsWith('#');

    $html = [];
    $html[] = form_open($action . "{$anchor}", hidden: [
        'uid' => $document['uid'],
    ]);

    $title = $document['original_filename'];

    $html[] = "<div class='row'>";
    $html[] = "<div class='col-4'>" . $document['document_type'] .
        '<span class="px-2 small">(' . Time::createFromTimestamp($document['created_at'])->humanize() . ')</span>'
        . '</div>';
    $html[] = "<div class='col-4'>" . img($document['uri'], attributes: [
        'width' => 64,
        'title' => $title,
        'alt' => $title,
    ]) . '</div>';
    $html[] = "<div class='col-4'>" . form_submit('submit', 'Delete') . '</div>';
    $html[] = '</div>';

    $html[] = '</form>';

    return implode(' ', $html);
}

/**
 * @brief Convert given datetime/timestamp to HTML input with type
 * datetime-local.
 */
function htmlDatetimeLocal(string $datetime): string 
{
    return date('Y-m-d\TH:i', strtotime($datetime));
}

/**
 * Convert given array of data to a table with form to do crud operations.
 *
 * @param array<array<string, mixed>> $rows
 * @param array<string>               $columns list of columns to show. If empty, all columns * are show.
 * @param string $primaryKey  Primary key of table.
 * @param array<string>               $hide    Hide these columns.
 */
function arrayToCrudTable(
    array $rows,
    AdminActionName $action,
    string $primaryKey = 'uid',
    array $columns = [],
    array $hide = []
): string 
{
    $html = [];
    $html[] = "<table class='table'>";

    $columns = count($columns) === 0 ? array_keys($rows[0]) : $columns;
    if(count($hide) > 0) {
        $columns = array_diff($columns, $hide);
    }

    $html[] = "<tr>";
    foreach($columns as $column) {
        $html[] = "<th>$column</th>";
    }
    $html[] = "</tr>";

    foreach($rows as $idx => $row) {
        unset($idx);
        $html[] = "<tr>";
        $html[] = arrayToCrudHtmlRow($row, $columns, primaryKey: $primaryKey, action: $action);
        $html[] = "</tr>";
    }

    $html[] = "</table>";
    return implode(' ', $html);
}

/**
 * @param array<string, mixed> $row
 * @param array<string>        $columns
 * @param AdminActionName      $action  Typically take to you to a view where you can do CRUD.
 * @param string $primaryKey Primary key of the entry.
 */
function arrayToCrudHtmlRow(array $row, array $columns, AdminActionName $action, string $primaryKey = 'uid'): string 
{
    Assert::that($row)->notEmpty();

    $html = [];

    // form
    $html[] = form_open("/admin/action/" . $action->value, hidden: [
        'uid' => $row[$primaryKey],
        $primaryKey => $row[$primaryKey],
    ]);

    foreach($row as $key => $value) {
        if(in_array($key, $columns)) {
            $html[] = "<td> $value </td>";
        }
    }

    $html[] = "<td>";
    $html[] = form_submit("submit", "Edit", extra: [
        'class' => 'btn btn-info',
    ]);
    $html[] = "</td>";

    $html[] = "</form>";
    return implode(' ', $html);
}

/**
 * Render patient document.
 *
 * @param array<string, string> $document Patient document.
 */
function renderPatientDocument(array $document): string {
    $filename = $document['original_filename'];
    $html = [];
    $html[] = "<img class='figure-img img-fluid' src='" . $document['uri'] . "' alt='$filename'>";
    $caption = $filename;
    $caption .= "| Document Type " . $document['document_type'];
    $html[] = "<figcaption class='figure-caption'>$caption </figcaption>";
    return implode(' ', $html);
}
