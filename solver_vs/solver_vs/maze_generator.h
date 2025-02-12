#include <stdio.h>
#include <stdlib.h>
#include <time.h>



/**
 * @brief Initializes the Union-Find data structure.
 *
 * @param parent Array representing the parent of each element.
 * @param rank Array representing the rank of each element.
 * @param size The number of elements.
 */
void initUnionFind(int* parent, int* rank, int size);

/**
 * @brief Finds the root of the element with path compression.
 *
 * @param parent Array representing the parent of each element.
 * @param x The element to find.
 * @return The root of the element.
 */
int find(int* parent, int x);

/**
 * @brief Merges two sets using rank.
 *
 * @param parent Array representing the parent of each element.
 * @param rank Array representing the rank of each element.
 * @param x The first element.
 * @param y The second element.
 */
void unionSets(int* parent, int* rank, int x, int y); 
/**
 * @brief Shuffles the edges in the maze generation process.
 *
 * @param edges Array of walls representing the edges.
 * @param size The number of edges.
 */
void shuffleEdges(Wall* edges, int size); 
/**
 * @brief Generates a maze using the Union-Find algorithm.
 *
 * @param room The room structure containing the maze information.
 */
void generateMaze(Room* room); 

/**
 * @brief Adds a new object to the room's list of objects.
 *
 * @param data The room structure.
 * @param i The x-coordinate of the object.
 * @param j The y-coordinate of the object.
 * @param objet The type of the object.
 */
void addnewliste_objet(Room* data, int i, int j, int objet); 
/**
 * @brief Adds an object to the maze at a random ground cell.
 *
 * @param data The room structure containing the maze information.
 * @param object The type of the object to add.
 */
void addObjectToMaze(Room* data, int object); 



Pair  generateRandomCoordinates(Room* data); 