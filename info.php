<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>How Huffman Compression Works</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* General page styles */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f9;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .content {
            max-width: 900px;
            margin: 40px auto;
            padding: 25px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #2c3e50;
            text-align: center;
            font-size: 2.5em;
            margin-bottom: 20px;
        }

        h2,
        h3 {
            color: #34495e;
            margin-top: 40px;
            font-size: 1.8em;
        }

        p,
        li {
            font-size: 1.1em;
            line-height: 1.8;
            margin-bottom: 20px;
            color: #555;
        }

        ul {
            padding-left: 20px;
            margin-bottom: 20px;
        }

        ul li {
            margin-bottom: 10px;
        }

        /* Image and graph container */
        .image-container,
        .graph {
            text-align: center;
            margin: 30px 0;
        }

        .image-container img,
        .graph img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .graph .description {
            font-size: 0.9em;
            color: #777;
            margin-top: 8px;
        }

        /* Footer styles */
        footer {
            text-align: center;
            margin-top: 50px;
            font-size: 0.9em;
            color: #999;
        }

        /* Buttons or links (if needed in the future) */
        a {
            color: #2980b9;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        /* Responsive adjustments */
        @media screen and (max-width: 768px) {
            h1 {
                font-size: 2em;
            }

            h2,
            h3 {
                font-size: 1.5em;
            }

            p,
            li {
                font-size: 1em;
            }
        }
    </style>
</head>

<body>

    <div class="content">
        <h1>Understanding Huffman Compression</h1>
        <p>
            Huffman coding is a popular lossless data compression algorithm. It works by assigning variable-length codes to input characters, with shorter codes assigned to more frequent characters. The algorithm is widely used in many compression formats such as ZIP files, GZIP, and image formats like JPEG. Huffman coding assigns variable length codewords to fixed length input characters based on their frequencies.
        </p>

        <h2>How Does Huffman Coding Work?</h2>
        <p>
            For instance, consider the string ABRACADABRA. There are a total of 11 characters in the string. This number should match the count in the ultimately determined root of the tree. Our frequencies are A=5, B=2, R=2, C=1, and D=1. We’ll create a tree using these frequencies.
        </p>

        <div class="image-container">
            <img src="assets/huffman-tree-example.png" alt="Example of a Huffman Tree">
            <p>An example of a Huffman tree for character frequencies.</p>
        </div>

        <h2>Step-by-Step Example</h2>
        <p>Let's go through an example to better understand how Huffman coding works.</p>

        <h3>Step 1: Frequency Calculation</h3>
        <p>
            Assume we are trying to encode the string "BCCABBDDA". First, we count the frequency of each character:
        </p>
        <ul>
            <li>A: 2</li>
            <li>B: 3</li>
            <li>C: 2</li>
            <li>D: 2</li>
        </ul>

        <div class="graph">
            <img src="assets/huffman-frequency-chart.png" alt="Frequency chart for Huffman coding example">
            <p class="description">A frequency chart for the string "BCCABBDDA".</p>
        </div>

        <h3>Step 2: Building the Huffman Tree</h3>
        <p>
            Now we create a binary tree based on these frequencies. We start by selecting the two least frequent characters, C and A, and combine them into a new node with a frequency of 4.
        </p>

        <div class="image-container">
            <img src="assets/huffman-tree-building.png" alt="Building the Huffman Tree">
            <p>Steps in building the Huffman tree.</p>
        </div>

        <h3>Step 3: Assigning Codes</h3>
        <p>
            Once the tree is built, we traverse it to assign binary codes to each character. Characters that appear more frequently will have shorter codes.
        </p>

        <div class="graph">
            <img src="assets/huffman-binary-codes.png" alt="Binary codes assigned using Huffman tree">
            <p class="description">The binary codes assigned to each character based on the Huffman tree.</p>
        </div>

        <h2>Why Use Huffman Coding?</h2>
        <p>
            Huffman coding is very efficient, especially when dealing with text that contains characters with varying frequencies. It's used in a variety of applications, including file compression tools and multimedia formats.
        </p>

        <h2>Key Benefits</h2>
        <ul>
            <li>Lossless compression, meaning no information is lost.</li>
            <li>Highly efficient for compressing text and other data types.</li>
            <li>Widely used in real-world applications, such as GZIP, PNG, and JPEG formats.</li>
        </ul>

        <footer>
            &copy; 2024 File Compression Tool
        </footer>
    </div>

</body>

</html>