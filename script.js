class MinHeap {
    constructor() {
        this.heap_array = [];
    }

    size() { return this.heap_array.length; }
    empty() { return this.size() === 0; }

    push(value) {
        this.heap_array.push(value);
        let current_index = this.size() - 1;
        while (current_index > 0) {
            let parent_index = Math.trunc((current_index - 1) / 2);
            if (this.heap_array[parent_index][0] < value[0]) break;
            [this.heap_array[parent_index], this.heap_array[current_index]] = [value, this.heap_array[parent_index]];
            current_index = parent_index;
        }
    }

    top() { return this.heap_array[0]; }

    pop() {
        if (this.size() > 1) {
            this.heap_array[0] = this.heap_array.pop();
            let current_index = 0;
            while (current_index < this.size()) {
                let child_index1 = current_index * 2 + 1, child_index2 = current_index * 2 + 2;
                if (child_index1 >= this.size()) break;
                let smaller_child = child_index2 >= this.size() || this.heap_array[child_index1][0] < this.heap_array[child_index2][0]
                    ? child_index1 : child_index2;
                if (this.heap_array[current_index][0] <= this.heap_array[smaller_child][0]) break;
                [this.heap_array[current_index], this.heap_array[smaller_child]] = [this.heap_array[smaller_child], this.heap_array[current_index]];
                current_index = smaller_child;
            }
        } else this.heap_array.pop();
    }
}

class Codec {
    constructor() { this.codes = {}; }

    getCodes(node, curr_code) {
        if (typeof node[1] === 'string') {
            this.codes[node[1]] = curr_code;
        } else {
            this.getCodes(node[1][0], curr_code + '0');
            this.getCodes(node[1][1], curr_code + '1');
        }
    }

    make_string(node) {
        return typeof node[1] === 'string' ? "'" + node[1] : '0' + this.make_string(node[1][0]) + '1' + this.make_string(node[1][1]);
    }

    make_tree(tree_string) {
        let node = [];
        if (tree_string[this.index] === "'") {
            node.push(tree_string[++this.index++]);
        } else {
            this.index++;
            node.push(this.make_tree(tree_string));
            this.index++;
            node.push(this.make_tree(tree_string));
        }
        return node;
    }

    encode(data) {
        let frequency = new Map();
        for (let char of data) frequency.set(char, (frequency.get(char) || 0) + 1);
        if (frequency.size === 0) return ["zer#", "Compression complete. Empty file!"];
        if (frequency.size === 1) {
            let [char, count] = [...frequency.entries()][0];
            return [`one#${char}#${count}`, "Compression complete for single-character file."];
        }

        let heap = new MinHeap();
        for (let [char, freq] of frequency) heap.push([freq, char]);
        while (heap.size() > 1) {
            let node1 = heap.top(); heap.pop();
            let node2 = heap.top(); heap.pop();
            heap.push([node1[0] + node2[0], [node1, node2]]);
        }
        let huffman_tree = heap.top();
        this.getCodes(huffman_tree, "");

        let binary_string = "";
        for (let char of data) binary_string += this.codes[char];
        let padding_length = (8 - binary_string.length % 8) % 8;
        binary_string += '0'.repeat(padding_length);

        let encoded_data = "";
        for (let i = 0; i < binary_string.length; i += 8) {
            encoded_data += String.fromCharCode(parseInt(binary_string.slice(i, i + 8), 2));
        }

        let tree_string = this.make_string(huffman_tree);
        return [`${tree_string.length}#${padding_length}#${tree_string}${encoded_data}`, `Compression complete. Compression ratio: ${(data.length / encoded_data.length).toFixed(2)}`];
    }

    decode(data) {
        let k = 0, temp = "";
        while (k < data.length && data[k] !== '#') temp += data[k++];
        if (temp === "zer") return ["", "Decompression complete. Empty file!"];
        if (temp === "one") {
            let [char, count] = data.slice(k + 1).split('#');
            return [char.repeat(Number(count)), "Decompression complete."];
        }

        let ts_length = parseInt(temp);
        data = data.slice(k + 1);
        temp = ""; k = 0;
        while (data[k] !== '#') temp += data[k++];
        let padding_length = parseInt(temp);
        let tree_string = data.slice(k + 1, k + 1 + ts_length);
        let encoded_data = data.slice(k + 1 + ts_length);

        this.index = 0;
        let huffman_tree = this.make_tree(tree_string);

        let binary_string = "";
        for (let i = 0; i < encoded_data.length; i++) {
            let binary_char = encoded_data.charCodeAt(i).toString(2).padStart(8, '0');
            binary_string += binary_char;
        }
        binary_string = binary_string.slice(0, -padding_length);

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

window.onload = function () {
    const encodeBtn = document.getElementById("encode");
    const decodeBtn = document.getElementById("decode");
    const uploadFile = document.getElementById("uploadfile");
    const submitBtn = document.getElementById("submitbtn");
    const step1 = document.getElementById("step1");
    const step3 = document.getElementById("feedback");

    let isSubmitted = false;
    let codecObj = new Codec();

    submitBtn.onclick = function () {
        const file = uploadFile.files[0];
        if (!file || file.name.split('.').pop().toLowerCase() !== 'txt') {
            alert("Please upload a valid .txt file.");
            return;
        }
        alert("File submitted!");
        isSubmitted = true;
        step1.innerHTML = "File uploaded successfully!";
    };

    encodeBtn.onclick = function () {
        const file = uploadFile.files[0];
        if (!file || !isSubmitted) {
            alert("Please upload and submit a file before compressing!");
            return;
        }
        const fileReader = new FileReader();
        fileReader.onload = function (event) {
            const [encodedString, outputMsg] = codecObj.encode(event.target.result);
            downloadFile(`${file.name.split('.')[0]}_compressed.txt`, encodedString);
            step3.innerHTML = outputMsg;
        };
        fileReader.readAsText(file, "UTF-8");
    };

    decodeBtn.onclick = function () {
        const file = uploadFile.files[0];
        if (!file || !isSubmitted) {
            alert("Please upload and submit a file before decompressing!");
            return;
        }
        const fileReader = new FileReader();
        fileReader.onload = function (event) {
            const [decodedString, outputMsg] = codecObj.decode(event.target.result);
            downloadFile(`${file.name.split('.')[0]}_decompressed.txt`, decodedString);
            step3.innerHTML = outputMsg;
        };
        fileReader.readAsText(file, "UTF-8");
    };

    function downloadFile(filename, content) {
        const element = document.createElement('a');
        element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(content));
        element.setAttribute('download', filename);
        document.body.appendChild(element);
        element.click();
        document.body.removeChild(element);
    }
};
