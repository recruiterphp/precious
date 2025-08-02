<?php

namespace Precious;

interface Singleton
{
    public static function instance(): self;
}
