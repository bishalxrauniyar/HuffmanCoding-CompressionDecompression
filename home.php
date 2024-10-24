<?php
// Start session
session_start();

// Check if the user is logged in, if not redirect them to the login page
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Store the username in a variable
$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to File Compression Tool</title>
    <link rel="stylesheet" href="style.css">
    <script>
        // MinHeap class for implementing the priority queue used in Huffman encoding
        class MinHeap {
            constructor() {
                this.heap_array = []; // Array to hold the heap elements
            }

            // Returns the number of elements in the heap
            size() {
                return this.heap_array.length;
            }

            // Checks if the heap is empty
            empty() {
                return this.size() === 0;
            }

            // Inserts a new value into the heap and re-balances it using up-heapify
            push(value) {
                this.heap_array.push(value);
                this.up_heapify();
            }

            // Up-heapify to maintain the min-heap property
            up_heapify() {
                let current_index = this.size() - 1;
                while (current_index > 0) {
                    let current_element = this.heap_array[current_index];
                    let parent_index = Math.trunc((current_index - 1) / 2);
                    let parent_element = this.heap_array[parent_index];

                    // Stop if the parent is smaller; otherwise, swap
                    if (parent_element[0] < current_element[0]) {
                        break;
                    }
                    this.heap_array[parent_index] = current_element;
                    this.heap_array[current_index] = parent_element;
                    current_index = parent_index;
                }
            }

            // Returns the top (minimum) element of the heap
            top() {
                return this.heap_array[0];
            }

            // Removes the top (minimum) element and re-balances the heap using down-heapify
            pop() {
                if (!this.empty()) {
                    let last_index = this.size() - 1;
                    this.heap_array[0] = this.heap_array[last_index];
                    this.heap_array.pop();
                    this.down_heapify();
                }
            }

            // Down-heapify to maintain the min-heap property after removal
            down_heapify() {
                let current_index = 0;
                while (current_index < this.size()) {
                    let child_index1 = current_index * 2 + 1;
                    let child_index2 = current_index * 2 + 2;

                    // If both children are out of bounds, exit
                    if (child_index1 >= this.size() && child_index2 >= this.size()) break;

                    // Determine which child is smaller
                    let smaller_child = child_index2 >= this.size() || this.heap_array[child_index1][0] < this.heap_array[child_index2][0] ?
                        child_index1 :
                        child_index2;

                    // Stop if the parent is smaller than the smaller child; otherwise, swap
                    if (this.heap_array[current_index][0] <= this.heap_array[smaller_child][0]) break;

                    [this.heap_array[current_index], this.heap_array[smaller_child]] = [this.heap_array[smaller_child], this.heap_array[current_index]];
                    current_index = smaller_child;
                }
            }
        }

        // Codec class handles Huffman encoding and decoding
        class Codec {
            // Recursive function to generate binary codes for each character from the Huffman tree
            getCodes(node, curr_code) {
                if (typeof node[1] === 'string') {
                    this.codes[node[1]] = curr_code; // Leaf node, assign code
                    return;
                }
                this.getCodes(node[1][0], curr_code + '0'); // Traverse left (append '0')
                this.getCodes(node[1][1], curr_code + '1'); // Traverse right (append '1')
            }

            // Converts Huffman tree into a string representation for encoding
            make_string(node) {
                return typeof node[1] === 'string' ? "'" + node[1] : '0' + this.make_string(node[1][0]) + '1' + this.make_string(node[1][1]);
            }

            // Rebuilds the Huffman tree from a string during decoding
            make_tree(tree_string) {
                let node = [];
                if (tree_string[this.index] === "'") {
                    this.index++;
                    node.push(tree_string[this.index]);
                    this.index++;
                    return node;
                }
                this.index++;
                node.push(this.make_tree(tree_string));
                this.index++;
                node.push(this.make_tree(tree_string));
                return node;
            }

            // Encodes the input data using Huffman coding
            encode(data) {
                let heap = new MinHeap(); // Initialize the min-heap
                let frequency = new Map(); // Map to store character frequencies

                // Step 1: Calculate the frequency of each character
                for (let char of data) {
                    frequency.set(char, (frequency.get(char) || 0) + 1);
                }

                // Handle edge case: empty input
                if (frequency.size === 0) return ["zer#", "Compression complete. Empty file!"];

                // Handle edge case: input with only one unique character
                if (frequency.size === 1) {
                    let [char, count] = [...frequency.entries()][0];
                    return [`one#${char}#${count}`, "Compression complete for single-character file."];
                }

                // Step 2: Build the Huffman tree by inserting frequencies into the min-heap
                for (let [char, freq] of frequency) {
                    heap.push([freq, char]);
                }

                // Step 3: Merge nodes in the heap to create the Huffman tree
                while (heap.size() > 1) {
                    let node1 = heap.top();
                    heap.pop();
                    let node2 = heap.top();
                    heap.pop();
                    heap.push([node1[0] + node2[0],
                        [node1, node2]
                    ]);
                }

                let huffman_tree = heap.top();
                this.codes = {};
                this.getCodes(huffman_tree, ""); // Generate codes from the Huffman tree

                // Step 4: Encode the input data into binary using the generated codes
                let binary_string = "";
                for (let char of data) {
                    binary_string += this.codes[char];
                }

                // Step 5: Add padding to make the binary string a multiple of 8
                let padding_length = (8 - binary_string.length % 8) % 8;
                binary_string += '0'.repeat(padding_length);

                // Step 6: Convert binary string to ASCII characters
                let encoded_data = "";
                for (let i = 0; i < binary_string.length; i += 8) {
                    encoded_data += String.fromCharCode(parseInt(binary_string.slice(i, i + 8), 2));
                }

                // Final encoded string includes the Huffman tree and padding info
                let tree_string = this.make_string(huffman_tree);
                let final_string = `${tree_string.length}#${padding_length}#${tree_string}${encoded_data}`;
                return [final_string, `Compression complete. Compression ratio: ${(data.length / final_string.length).toFixed(2)}`];
            }

            // Decodes the encoded data using the Huffman tree
            decode(data) {
                let k = 0,
                    temp = "";

                // Step 1: Handle edge cases (empty or single-character input)
                while (k < data.length && data[k] !== '#') temp += data[k++];
                if (temp === "zer") return ["", "Decompression complete. Empty file!"];
                if (temp === "one") {
                    let [char, count] = data.slice(k + 1).split('#');
                    return [char.repeat(Number(count)), "Decompression complete."];
                }

                // Step 2: Rebuild the Huffman tree from the encoded string
                let ts_length = parseInt(temp);
                data = data.slice(k + 1);
                temp = "";
                k = 0;
                while (data[k] !== '#') temp += data[k++];
                let padding_length = parseInt(temp);
                let tree_string = data.slice(k + 1, k + 1 + ts_length);
                let encoded_data = data.slice(k + 1 + ts_length);

                this.index = 0;
                let huffman_tree = this.make_tree(tree_string);

                // Step 3: Convert the encoded data back into binary
                let binary_string = "";
                for (let i = 0; i < encoded_data.length; i++) {
                    let binary_char = encoded_data.charCodeAt(i).toString(2).padStart(8, '0');
                    binary_string += binary_char;
                }
                binary_string = binary_string.slice(0, -padding_length); // Remove padding

                // Step 4: Decode the binary string using the Huffman tree
                let decoded_data = "",
                    node = huffman_tree;
                for (let bit of binary_string) {
                    node = bit === '1' ? node[1] : node[0];
                    if (typeof node[0] === 'string') {
                        decoded_data += node[0];
                        node = huffman_tree;
                    }
                }

                return [decoded_data, "Decompression complete."];
            }
        }

        // UI interactions and file handling
        window.onload = function() {
            const encodeBtn = document.getElementById("encode");
            const decodeBtn = document.getElementById("decode");
            const uploadFile = document.getElementById("uploadfile");
            const submitBtn = document.getElementById("submitbtn");
            const step1 = document.getElementById("step1");
            const step2 = document.getElementById("step2");
            const step3 = document.getElementById("feedback");

            let isSubmitted = false;
            let codecObj = new Codec();

            // Handle file submission
            submitBtn.onclick = function() {
                const file = uploadFile.files[0];
                if (!file) {
                    alert("No file uploaded. Please upload a valid .txt file and try again!");
                    return;
                }
                if (file.name.split('.').pop().toLowerCase() !== 'txt') {
                    alert("Invalid file type. Please upload a .txt file.");
                    return;
                }
                alert("File submitted!");
                isSubmitted = true;
                step1.innerHTML = "File uploaded successfully!";
            };

            // Handle file compression
            encodeBtn.onclick = function() {
                const file = uploadFile.files[0];
                if (!file || !isSubmitted) {
                    alert("Please upload and submit a file before compressing!");
                    return;
                }

                const fileReader = new FileReader();
                fileReader.onload = function(event) {
                    const fileContent = event.target.result;
                    const [encodedString, outputMsg] = codecObj.encode(fileContent);
                    downloadFile(`${file.name.split('.')[0]}_compressed.txt`, encodedString);
                    step3.innerHTML = outputMsg;
                };
                fileReader.readAsText(file, "UTF-8");
            };

            // Handle file decompression
            decodeBtn.onclick = function() {
                const file = uploadFile.files[0];
                if (!file || !isSubmitted) {
                    alert("Please upload and submit a file before decompressing!");
                    return;
                }

                const fileReader = new FileReader();
                fileReader.onload = function(event) {
                    const fileContent = event.target.result;
                    const [decodedString, outputMsg] = codecObj.decode(fileContent);
                    downloadFile(`${file.name.split('.')[0]}_decompressed.txt`, decodedString);
                    step3.innerHTML = outputMsg;
                };
                fileReader.readAsText(file, "UTF-8");
            };

            // Helper function to download files
            function downloadFile(filename, content) {
                const element = document.createElement('a');
                element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(content));
                element.setAttribute('download', filename);
                document.body.appendChild(element);
                element.click();
                document.body.removeChild(element);
            }

            document.getElementById("uploadForm").onsubmit = function(event) {
                event.preventDefault(); // Prevent the default form submission

                const formData = new FormData(this);

                fetch("upload_to_db.php", {
                        method: "POST",
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(data.message);
                            // Optionally, refresh the history or display new uploads
                        } else {
                            alert(data.error);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while uploading the file.');
                    });
            };

        };
    </script>
    <style>
        /* General Styling */
        body,
        html {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: 'Arial', sans-serif;
            background-image: url('assets/bg.jpg');
            /* Background image */
            background-size: cover;
            background-position: center;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            color: #333;
        }

        .container {
            width: 90%;
            max-width: 900px;
            margin: 20px auto;
            padding: 20px;
            background: rgba(255, 255, 255, 0.95);
            /* Slightly transparent background */
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 20px;
        }

        /* Navbar Styling */
        .navbar {
            background-color: rgba(255, 99, 71, 0.9);
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
            height: 50px;
            /* Reduced height */
            transition: background-color 0.3s ease;
        }

        .navbar:hover {
            background-color: rgba(255, 99, 71, 1);
            /* Solid background on hover */
        }

        .navbar-brand {
            display: flex;
            align-items: center;
        }

        .navbar-brand h1 {
            margin-left: 10px;
            font-size: 1.8em;
            /* Smaller font size */
            color: #fff;
        }

        .navbar a {
            color: white;
            margin-left: 15px;
            font-size: 1em;
            /* Adjusted font size for modern look */
            transition: color 0.3s ease;
        }

        .navbar a:hover {
            color: #FFD700;
        }

        /* Form Styling */
        form {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
        }

        input[type="file"],
        select {
            width: 100%;
            padding: 10px;
            font-size: 1em;
            border: 2px solid #FFA500;
            border-radius: 5px;
            background-color: #f9f9f9;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: border-color 0.3s ease;
        }

        input[type="file"]:focus,
        select:focus {
            border-color: #FF4500;
        }

        select {
            background-color: white;
        }

        /* Button Styling */
        .btn {
            background-color: rgba(255, 99, 71, 0.8);
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1.1em;
            font-weight: bold;
            transition: background-color 0.3s ease, transform 0.3s ease;
            width: 100%;
            /* Make button full width for modern look */
            max-width: 200px;
        }

        .btn:hover {
            background-color: rgba(255, 99, 71, 1);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        /* Step Section Styling */
        .step {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 100%;
            /* Make sure step takes full width */
        }

        h2 {
            font-size: 1.8em;
            color: #2c3e50;
            margin-bottom: 15px;
        }

        /* Footer Styling */
        footer {
            text-align: center;
            padding: 10px 0;
            background-color: rgba(44, 62, 80, 0.9);
            color: white;
            box-shadow: 0 -2px 4px rgba(0, 0, 0, 0.1);
            width: 100%;
            position: sticky;
            bottom: 0;
        }

        footer p {
            margin: 0;
            font-size: 1em;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                width: 95%;
                padding: 15px;
            }

            .navbar {
                flex-direction: column;
                gap: 5px;
                height: auto;
            }

            .navbar-brand h1 {
                font-size: 1.5em;
            }

            .navbar a {
                font-size: 0.9em;
                margin-left: 0;
                margin-right: 10px;
            }

            h2 {
                font-size: 1.5em;
            }

            .btn {
                padding: 10px 20px;
                max-width: 100%;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <div class="navbar-brand">
            <a href="home.php">
                <img src="assets/logo.png" alt="logo" width="50" height="50">
                <h1>File Compression Tool</h1>
            </a>
        </div>

        <div class="navbar-links">
            <a href="info.html" target="_blank">About Compression Technique. </a> <a href="pdf.html" target="_blank">PDF Compressor 💡</a>
        </div>

        <div class="greeting-navbar">
            <p>Welcome, <b><?php echo htmlspecialchars($username); ?>!</b></p>
            <a href="logout.php">Logout</a>
        </div>
    </nav>

    <main class="container">
        <section class="step" id="step1">
            <h2>Step 1: Upload Your File (.txt Only)</h2>
            <form id="fileform">
                <input type="file" id="uploadfile" accept=".txt">
                <button type="button" id="submitbtn" class="btn">Upload</button>
            </form>
        </section>

        <section class="step" id="step2">
            <h2>Step 2: Choose an Action</h2>
            <button type="button" id="encode" class="btn action-btn">Compress</button>
            <button type="button" id="decode" class="btn action-btn">Decompress</button>
        </section>

        <section id="feedback" class="step">
            <h2>Sit Back and Relax</h2>
            <button class="btn" onclick="location.reload()">Reload</button>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 File Compression Tool</p>
    </footer>
</body>

</html>