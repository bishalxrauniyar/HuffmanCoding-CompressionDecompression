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
            let smaller_child = child_index2 >= this.size() || this.heap_array[child_index1][0] < this.heap_array[child_index2][0]
                ? child_index1
                : child_index2;

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
            heap.push([node1[0] + node2[0], [node1, node2]]);
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
        let k = 0, temp = "";

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
        let decoded_data = "", node = huffman_tree;
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
window.onload = function () {
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
    submitBtn.onclick = function () {
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
    encodeBtn.onclick = function () {
        const file = uploadFile.files[0];
        if (!file || !isSubmitted) {
            alert("Please upload and submit a file before compressing!");
            return;
        }

        const fileReader = new FileReader();
        fileReader.onload = function (event) {
            const fileContent = event.target.result;
            const [encodedString, outputMsg] = codecObj.encode(fileContent);
            downloadFile(`${file.name.split('.')[0]}_compressed.txt`, encodedString);
            step3.innerHTML = outputMsg;
        };
        fileReader.readAsText(file, "UTF-8");
    };

    // Handle file decompression
    decodeBtn.onclick = function () {
        const file = uploadFile.files[0];
        if (!file || !isSubmitted) {
            alert("Please upload and submit a file before decompressing!");
            return;
        }

        const fileReader = new FileReader();
        fileReader.onload = function (event) {
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
};
