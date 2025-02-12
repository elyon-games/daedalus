#pragma once
#pragma region Constants

#define MAX_PLACED_OBJECTS 20
#define MAX_TRAPS 5
#define MAX_FLOORS 10
#define MAX_ROOMS_X 4
#define MAX_ROOMS_Y 4
#define MAX_ROOM_SIZE 24

#pragma endregion


#pragma region TileCodes

/**
 * @brief Enum for tile types
*/
enum Tiles {
	EMPTY,
	GROUND,
	WALL,
	ENTRANCE,

	STAIRS_UP,
	STAIRS_DOWN,

	FENCE,
	FENCE_ELECTRIFIED,
	FENCE_CUT,

	DOOR_RED_CLOSED,
	DOOR_RED_OPENED,

	PLANK,

	WATER,
	FIRE,

	ELECTRIC_ON,
	ELECTRIC_OFF,

	ICE,

	CONVEYOR_BELT_NORTH,
	CONVEYOR_BELT_EAST,
	CONVEYOR_BELT_SOUTH,
	CONVEYOR_BELT_WEST,

	KEY_RED,
	KEY_BLUE,
	KEY_GREEN,
	KEY_YELLOW,

	LEVEL_START,
	LEVEL_END,

	LEVER,

	DOOR_GREEN_CLOSED,
	DOOR_GREEN_OPENED,
	DOOR_YELLOW_CLOSED,
	DOOR_YELLOW_OPENED,
	DOOR_BLUE_CLOSED,
	DOOR_BLUE_OPENED,

	SHEARS,

	TILE_NONEXISTANT = 255
};

#pragma endregion




#pragma region Errors

/**
 * @brief Enum for errors
*/
enum Errors {
	NO_ERROR = 0,
	ERROR_VARIABLE_NULL,
	ERROR_OUT_OF_GRID,
	ERROR_NOT_ENOUGH_MEMORY,
	ERROR_INVALID_TILE,
	ERROR_VARIABLE_ZERO,
	ERROR_MAX_OVERFLOW
};

#pragma endregion

#pragma region Types

/**
 * @brief The type of a tile. Using an unsigned char for memory optimization
*/
typedef unsigned char Tile;
/**
 * @brief A cardinal direction. Using an unsigned char since there are only 8 directions
*/
typedef unsigned char Direction;

/**
 * @brief The type of a coordinate (x, y, width, height, etc.). Using an unsigned char for maximum memory optimization and the type of the cordonées  
*/
typedef struct Pair {
	int x;
	int y;
	int type;

} Pair;

/**
 * @brief Error type. Using an unsigned char because there are less than 255 possible errors
*/
typedef unsigned char Error;

/**
 * @brief Boolean value type. Using a char since booleans only have 2 states
*/
typedef char Boolean;

#pragma endregion


#pragma region Structures




/**
 * @brief Structure for a puzzle/maze room
 *
 * @var Room::tab
 *      Array representing the maze. A 1D array containing all the cells in the room.
 *      `tab[y*Col + x]` will return the cell at coordinates (x, y).
 * @var Room::info
 *      Array of Pair structures containing information about the objects positioned in the room.
 * @var Room::Row
 *      Number of rows in the room (in cells).
 * @var Room::Col
 *      Number of columns in the room (in cells).
 * @var Room::src
 *      Starting coordinates (source) in the room, used for solver purposes.
 * @var Room::dest
 *      Ending coordinates (destination) in the room, used for solver purposes.
 * @var Room::Key_Red
 *      Number of red keys present in the room.
 * @var Room::Key_Blue
 *      Number of blue keys present in the room.
 * @var Room::Key_Green
 *      Number of green keys present in the room.
 * @var Room::Key_Yellow
 *      Number of yellow keys present in the room.
 */
typedef struct Room {
	int* tab;        // Array representing the maze
	Pair* info;      // Information about the position of objects
	int Row;         // Number of rows
	int Col;         // Number of columns
	Pair* src;       // Starting coordinates (source)
	Pair* dest;      // Ending coordinates (destination)
	int Key_Red;     // Number of red keys
	int Key_Blue;    // Number of blue keys
	int Key_Green;   // Number of green keys
	int Key_Yellow;  // Number of yellow keys
	int Cut;
	int elec; 
} Room;

typedef struct Wall {
	int x1, y1, x2, y2;
} Wall;

Room* new_Room(int row, int col);
void printMaze(Room* room);

void freeRoom(Room* room);

#pragma endregion
