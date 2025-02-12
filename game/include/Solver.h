#define COL  10
#define ROW  9

//-------------------------------------------//
// Structure to represent a cell with its costs g, h, and f, as well as its parent
typedef struct Cell {
    int parent_x;
    int parent_y;
    double f, h, g;
} Cell;

// Create an open list node containing information as <f, <i, j>>
typedef struct OpenListNode {
    double f;
    int x;
    int y;
    struct OpenListNode* next;
} OpenListNode;

// Function to initiate the search within a given room
void recherche(Room* meta);

//-------------Linked List Functions----------------------//

/**
 * @brief Inserts a new node into the open list in a sorted manner based on the f value.
 *
 * @param openList The head of the open list.
 * @param f The f value of the new node.
 * @param i The x-coordinate of the new node.
 * @param j The y-coordinate of the new node.
 * @return The new head of the open list.
 */
OpenListNode* insertOpenList(OpenListNode* openList, double f, int i, int j);

/**
 * @brief Removes the first node from the open list.
 *
 * @param openList The head of the open list.
 * @return The new head of the open list.
 */
OpenListNode* removeOpenList(OpenListNode* openList);

/**
 * @brief Displays the content of the open list.
 *
 * @param list The head of the open list.
 */
void affichage(OpenListNode* list);

//----------------------Functions for A*---------------------//
/**
 * @brief Calculates the heuristic (Manhattan distance) for the A* algorithm.
 *
 * @param i The x-coordinate of the current cell.
 * @param j The y-coordinate of the current cell.
 * @param dest The destination pair containing the ending coordinates.
 * @return The Manhattan distance between the current cell and the destination cell, or -1 if the input is invalid.
 */
double calcule_heuristiques(int i, int j, Pair* dest); 
/**
 * @brief Checks if a cell is within the grid bounds.
 *
 * @param i The x-coordinate of the cell.
 * @param j The y-coordinate of the cell.
 * @param row The number of rows in the grid.
 * @param col The number of columns in the grid.
 * @return True if the cell is within the grid bounds, false otherwise.
 */
bool is_value(int i, int j, int row, int col);

/**
 * @brief Checks if a cell is the destination.
 *
 * @param i The x-coordinate of the cell.
 * @param j The y-coordinate of the cell.
 * @param dest The destination pair containing the ending coordinates.
 * @return True if the cell is the destination, false otherwise.
 */
bool isDestination(int i, int j, Pair* dest);

/**
 * @brief Checks if a cell is unblocked (1 if free, 0 if blocked).
 *
 * @param data The room data.
 * @param i The x-coordinate of the cell.
 * @param j The y-coordinate of the cell.
 * @return true if the cell is unblocked, false otherwise.
 */
bool is_unBlocked(Room* data, int i, int j);

/**
 * @brief The main function of the A* algorithm. This function finds the shortest path between the source and the destination in a grid.
 *
 * @param data The room data containing the grid and its properties.
 * @param src The source pair containing the starting coordinates.
 * @param dest The destination pair containing the ending coordinates.
 * @return An integer indicating the result of the search:
 * - -1: Source is invalid (out of grid bounds).
 * - -2: Destination is invalid (out of grid bounds).
 * - -3: Source or destination is blocked.
 * - -4: Source and destination are the same.
 * - 1: Path found and successfully traced.
 * - 0: Path not found.
 */
int StarSearch(Room* data, Pair* src, Pair* dest);

/**
 * @brief Retraces the path from the destination to the source.
 *
 * @param test The cell data.
 * @param dest The destination pair.
 */
void trace_soluce(Cell* test, Pair* dest);
