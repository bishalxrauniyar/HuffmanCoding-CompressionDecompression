<?php

class MinHeap
{
    private $heap_array = [];

    public function size()
    {
        return count($this->heap_array);
    }

    public function push($value)
    {
        $this->heap_array[] = $value;
        $this->up_heapify();
    }

    private function up_heapify()
    {
        $index = $this->size() - 1;
        while ($index > 0) {
            $parent_index = (int)(($index - 1) / 2);
            if ($this->heap_array[$parent_index][0] <= $this->heap_array[$index][0]) break;
            $this->swap($parent_index, $index);
            $index = $parent_index;
        }
    }

    public function pop()
    {
        if ($this->size() === 0) return null;
        $min = $this->heap_array[0];
        $this->heap_array[0] = $this->heap_array[$this->size() - 1];
        array_pop($this->heap_array);
        $this->down_heapify();
        return $min;
    }

    private function down_heapify()
    {
        $index = 0;
        while (true) {
            $left = 2 * $index + 1;
            $right = 2 * $index + 2;
            if ($left >= $this->size()) break;
            $smallest = ($right < $this->size() && $this->heap_array[$right][0] < $this->heap_array[$left][0]) ? $right : $left;
            if ($this->heap_array[$index][0] <= $this->heap_array[$smallest][0]) break;
            $this->swap($index, $smallest);
            $index = $smallest;
        }
    }

    private function swap($i, $j)
    {
        $temp = $this->heap_array[$i];
        $this->heap_array[$i] = $this->heap_array[$j];
        $this->heap_array[$j] = $temp;
    }
}

class Codec
{
    private $codes = [];
    private $index = 0;

    public function encode($data)
    {
        $heap = new MinHeap();
        $frequency = [];

        foreach (str_split($data) as $char) {
            if (!isset($frequency[$char])) $frequency[$char] = 0;
            $frequency[$char]++;
        }

        foreach ($frequency as $char => $freq) {
            $heap->push([$freq, $char]);
        }

        while ($heap->size() > 1) {
            $node1 = $heap->pop();
            $node2 = $heap->pop();
            $heap->push([$node1[0] + $node2[0], [$node1, $node2]]);
        }

        $huffman_tree = $heap->pop();
        $this->generateCodes($huffman_tree, "");

        $binary_string = "";
        foreach (str_split($data) as $char) {
            $binary_string .= $this->codes[$char];
        }

        $padding_length = (8 - strlen($binary_string) % 8) % 8;
        $binary_string .= str_repeat('0', $padding_length);
        $encoded_data = pack("H*", bin2hex($binary_string));

        $tree_string = $this->serializeTree($huffman_tree);
        return ["{$tree_string}{$encoded_data}", "File compressed successfully"];
    }

    public function decode($data)
    {
        $tree_string = substr($data, 0, strpos($data, '#'));
        $encoded_data = substr($data, strpos($data, '#') + 1);
        $huffman_tree = $this->deserializeTree($tree_string);
        return [$this->decodeBinary($encoded_data, $huffman_tree), "Decompression complete."];
    }

    private function generateCodes($node, $curr_code)
    {
        if (is_string($node[1])) {
            $this->codes[$node[1]] = $curr_code;
            return;
        }
        $this->generateCodes($node[1][0], $curr_code . '0');
        $this->generateCodes($node[1][1], $curr_code . '1');
    }

    private function serializeTree($node)
    {
        // If it's a leaf node, store the character
        if (is_string($node[1])) {
            return "'" . $node[1];  // Use ' to indicate a leaf node with the character
        }

        // Recursively serialize left and right subtrees
        return "0" . $this->serializeTree($node[1][0]) . "1" . $this->serializeTree($node[1][1]);
    }


    private function deserializeTree($tree_string)
    {
        // Check if the index is beyond the length of the tree string
        if ($this->index >= strlen($tree_string)) {
            throw new Exception("Tree string is corrupted or incomplete.");
        }

        // If we encounter a leaf node (denoted by `'`), return the character
        if ($tree_string[$this->index] === "'") {
            $this->index++;
            if ($this->index >= strlen($tree_string)) {
                throw new Exception("Corrupted tree string, missing character after leaf node.");
            }
            return [null, $tree_string[$this->index++]];  // Return leaf node with character
        }

        // Recursively build left and right subtrees
        $this->index++;  // Move past '0' indicating left node
        $left = $this->deserializeTree($tree_string);
        $this->index++;  // Move past '1' indicating right node
        $right = $this->deserializeTree($tree_string);

        return [$left, $right];
    }



    private function decodeBinary($binary_string, $tree)
    {
        $decoded = '';
        $node = $tree;
        foreach (str_split($binary_string) as $bit) {
            $node = $bit === '0' ? $node[0] : $node[1];
            if (is_string($node[1])) {
                $decoded .= $node[1];
                $node = $tree;
            }
        }
        return $decoded;
    }
}
