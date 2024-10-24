<?php
class Codec
{
    private $codes = [];
    private $index = 0;

    public function encode($data)
    {
        $heap = new MinHeap();
        $frequency = [];

        // Step 1: Calculate frequency of characters
        foreach (str_split($data) as $char) {
            if (!isset($frequency[$char])) {
                $frequency[$char] = 0;
            }
            $frequency[$char]++;
        }

        // Handle edge case for empty or single-character files
        if (count($frequency) === 0) {
            return ["", "Compression complete. Empty file!"];
        } elseif (count($frequency) === 1) {
            return ["", "Compression skipped. File contains only one unique character."];
        }

        // Step 2: Build the Huffman tree
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

        // Step 3: Encode the input data using generated codes
        $binary_string = "";
        foreach (str_split($data) as $char) {
            $binary_string .= $this->codes[$char];
        }

        // Step 4: Add padding to make the binary string a multiple of 8
        $padding_length = (8 - strlen($binary_string) % 8) % 8;
        $binary_string .= str_repeat('0', $padding_length);

        // Step 5: Convert binary string to binary data (pack into ASCII)
        $encoded_data = pack("H*", bin2hex($binary_string));

        // Step 6: Serialize the tree into a compact string
        $tree_string = $this->serializeTree($huffman_tree);

        // Step 7: Check if compressed size is larger, return original if so
        $final_string = "{$tree_string}{$encoded_data}";
        if (strlen($final_string) >= strlen($data)) {
            return [$data, "Compression skipped. Compressed file larger than original."];
        }

        return [$final_string, "Compression successful. Compression ratio: " . (strlen($data) / strlen($final_string))];
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
        return is_string($node[1]) ? "'{$node[1]}" : "0{$this->serializeTree($node[1][0])}1{$this->serializeTree($node[1][1])}";
    }

    private function deserializeTree($tree_string)
    {
        if ($tree_string[$this->index] === "'") {
            return [null, $tree_string[++$this->index]];
        }
        $this->index++;
        $left = $this->deserializeTree($tree_string);
        $this->index++;
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
