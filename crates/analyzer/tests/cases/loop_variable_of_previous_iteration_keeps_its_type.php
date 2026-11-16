<?php

declare(strict_types=1);

final class Item
{
    public function name(): string
    {
        return 'item';
    }
}

function takes(?Item $i): void
{
    var_dump($i);
}

function takes_int(int $i): void
{
    var_dump($i);
}

/** @param list<int> $xs */
function walk(array $xs): void
{
    foreach ($xs as $_) {
        /** @mago-expect analysis:possibly-undefined-variable */
        takes($item);
        $item = new Item();
    }
}

/** @param list<int> $xs */
function method_after_null_check(array $xs): void
{
    foreach ($xs as $_) {
        /** @mago-expect analysis:possibly-undefined-variable */
        if ($item !== null) {
            echo $item->name();
        }

        $item = new Item();
    }
}

/** @param list<int> $xs */
function closure_use(array $xs): void
{
    foreach ($xs as $_) {
        /** @mago-expect analysis:possibly-undefined-variable */
        $f = function () use ($a): ?string {
            return $a;
        };
        echo $f() ?? '';
        $a = 's';
    }
}

function assigned_before_continue(): int
{
    for ($i = 0; $i < 10; $i++) {
        if (rand(0, 1)) {
            $name = 5;
            continue;
        }
    }

    /** @mago-expect analysis:possibly-undefined-variable */
    return $name;
}

/** @param list<int> $xs */
function possibly_undefined_read(array $xs): void
{
    foreach ($xs as $x) {
        if ($x > 0) {
            $n = 1;
        }

        /**
         * @mago-expect analysis:possibly-undefined-variable
         * @mago-expect analysis:possibly-invalid-argument
         */
        takes_int($n);
        $n = 'str';
    }
}

/** @param list<int> $xs */
function read_in_inner_loop(array $xs): void
{
    foreach ($xs as $_) {
        foreach ($xs as $_y) {
            /** @mago-expect analysis:possibly-undefined-variable */
            takes($item);
        }

        $item = new Item();
    }
}

/** @param list<int> $xs */
function continue_assigns_carried_value(array $xs): void
{
    foreach ($xs as $x) {
        /**
         * @mago-expect analysis:possibly-undefined-variable
         * @mago-expect analysis:possibly-invalid-argument
         */
        takes_int($v);
        if ($x > 1) {
            $u = 1;
        }

        if ($x > 0) {
            /** @mago-expect analysis:possibly-undefined-variable */
            $v = $u;
            continue;
        }

        $u = 'str';
        $v = 2;
    }
}
