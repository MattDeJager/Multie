<?php

namespace boost\multie\helpers;

interface VueAdminTableHelperInterface
{
  public static function actions(): array;
  public static function data($entries): array;
  public static function columns(): array;
}