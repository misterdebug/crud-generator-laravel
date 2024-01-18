<?php

namespace App\Services;

use Spatie\Html;

class HtmlExtended extends Html
{
    public function string(...$args): Html\Elements\Input
    {
        return html()->text($args);
    }
    public function integer(...$args): Html\Elements\Input
    {
        return html()->text($args);
    }
}