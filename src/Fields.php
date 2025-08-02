<?php

namespace Precious;

use Iterator;

/**
 * @implements Iterator<int, Field>
 */
class Fields implements Iterator
{
    private int $position;

    /**
     * @param array<Field> $fields
     * @throws NameClashFieldException
     * @returns self
     */
    public function __construct(private readonly array $fields) {
        $this->position = 0;
        self::ensureUniqueNames(
            array_map(fn($field) => $field->name(), $fields)
        );
    }

    public function rewind(): void {
        $this->position = 0;
    }

    public function current(): Field {
        return $this->fields[$this->position];
    }

    public function key(): int {
        return $this->position;
    }

    public function next(): void {
        ++$this->position;
    }

    public function valid(): bool {
        return isset($this->fields[$this->position]);
    }

    /**
     * @param array<string> $declaredNames;
     * @throws NameClashFieldException
     */
    private static function ensureUniqueNames(array $declaredNames): void
    {
        $uniqueNames = array_unique($declaredNames);
        if (count($declaredNames) !== count($uniqueNames)) {
            [$duplicateFieldName] = array_values(array_diff_assoc($declaredNames, $uniqueNames));
            throw new NameClashFieldException(
                "Cannot redeclare field `{$duplicateFieldName}`"
            );
        }
    }
}
