What is Huffman Coding?
First of all, let's start with codification in general. When we transmit information, need to convert the data (text, music, video, etc.) into binary code. To do this, we assign a code to each piece of data so we can distinguish them, and decode them later.

If we take a string as an example, for example 'ABACA', we could assign a same-length code to each one of the unique symbols (usually called naive coding).

Naive Coding
A	00
B	01
C	11
Encoded output:

00 01 00 11 00
10 char * 8 bits/char = 80 bits

With the obtained table, we could later translate the binary codes back to the text without loosing information on the process, but is this the best way to do this?

Huffman coding improves this process, being a lossless data compression algorithm that assigns variable-length codes based on the frequencies of our input characters.

To determine how to assign the codes to each symbol, we have to take the following steps:
Analyse the frequency of each character
Build the binary tree:
Take the pair of nodes with the least frequency
Iterate until left with one node
Starting at the root, label the edge to the left child as 0 and the edge to the right child as 1. Iterate for every child.
Go over the tree from each leaf to the root, writing down the labeled binary numbers, to generate the code word for each symbol.
Once we get the code words, we will notice that using this method, shorter words are assigned to the most frequent symbols. This way, the resulted encoded string is shorter!
