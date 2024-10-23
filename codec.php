<?php
class Codec
{
    public function encode($data)
    {
        // Simple base64 encoding for demonstration purposes
        $encoded_data = base64_encode($data);
        return [$encoded_data, "File compressed successfully."];
    }

    public function decode($data)
    {
        // Simple base64 decoding for demonstration purposes
        $decoded_data = base64_decode($data);
        return [$decoded_data, "File decompressed successfully."];
    }
}
