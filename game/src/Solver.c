//#include <fcgi_stdio.h>
#include"main.h"



//https://www.geeksforgeeks.org/a-search-algorithm/


//---------------------function de manipulaiton des liste chainé---------------------//

void affichage(OpenListNode* list) {
	if (list != NULL) {
		OpenListNode* tmp = list;
		printf("valeur de f : %lf\n", tmp->f);
		printf("valeur de x : %d\n", tmp->x);
		printf("valeur de y : %d\n", tmp->y);
		while (tmp->next != NULL)
		{
			tmp = tmp->next;
			printf("valeur de f : %lf\n", tmp->f);
			printf("valeur de x : %d\n", tmp->x);
			printf("valeur de y : %d\n", tmp->y);
			
		}
		return;
	}
}


OpenListNode* insertOpenList(OpenListNode* openList, double f, int i, int j) {
	if (i < 0 || j < 0 || f < 0) {
		return openList; // Retourne la liste originale si des valeurs invalides sont fournies
	}

	OpenListNode* newNode = (OpenListNode*)malloc(sizeof(OpenListNode));
	if (newNode == NULL) {
		return openList; // Retourne la liste originale si l'allocation de mémoire échoue
	}

	newNode->f = f;
	newNode->x = i;
	newNode->y = j;
	newNode->next = NULL;

	if (openList == NULL || openList->f > f) {
		newNode->next = openList;
		return newNode; // Retourne la nouvelle tête de la liste
	}
	else {
		OpenListNode* current = openList;
		while (current->next != NULL && current->next->f < f) {
			current = current->next;
		}
		newNode->next = current->next;
		current->next = newNode;
		return openList; // Retourne la tête originale de la liste
	}
}


OpenListNode*  removeOpenList(OpenListNode* openList) {
	if (openList == NULL) {
		return (NULL) ;
	}
	
	OpenListNode* temp = openList;
	openList = openList->next;
	free(temp);
	return(openList);
}


//-----------------------------------------------function algo------------------------------------------------// 

double calcule_heuristiques(int i, int j, Pair* dest) {
    if (i < 0 && j < 0 && dest == NULL) {
        return -1;
    }
    return abs(i - dest->x) + abs(j - dest->y);
}


bool is_value(int i, int j, int row, int col) {
    if (i > col || i < 0 || j > row || j < 0) {
        return false;
    }
    return (i >= 0) && (i < row) && (j >= 0) && (j < col);
}


bool isDestination(int i, int j, Pair* dest) {
    return i == dest->x && j == dest->y;
}


bool is_unBlocked(Room* data, int i, int j) {
    if (data == NULL || i < 0 || j < 0) {
        return(-1);
    }

    switch (data->tab[data->Col * j + i]) {

    case STAIRS_UP:
    case STAIRS_DOWN:
    case EMPTY:
    case GROUND:
    case FENCE_CUT:
    case DOOR_RED_OPENED:
    case DOOR_GREEN_OPENED:
    case DOOR_YELLOW_OPENED:
    case DOOR_BLUE_OPENED:
    case LEVEL_START:
    case LEVEL_END:
    case LEVER:
    case KEY_RED:
    case KEY_BLUE:
    case KEY_GREEN:
    case KEY_YELLOW:
    case CONVEYOR_BELT_NORTH:
    case CONVEYOR_BELT_EAST:
    case CONVEYOR_BELT_SOUTH:
    case CONVEYOR_BELT_WEST:
    case ENTRANCE:
    case PLANK:
    case WATER:
    case FIRE:
    case ICE:
    case SHEARS:
        return (1);

    case FENCE:
        if (data->Cut == 1) {
            return(1);
        }
        else {
            return(0);
        }

    case FENCE_ELECTRIFIED:
        if(data->elec == ELECTRIC_OFF && data->Cut == 1)
        {
            return (1); 
        }
        else {
            return (0); 
        }
    case DOOR_RED_CLOSED:
    case DOOR_GREEN_CLOSED:
    case DOOR_BLUE_CLOSED:
    case DOOR_YELLOW_CLOSED:
    case WALL:
        return (0);
    default:
        return (0);
    }

} 



void trace_soluce(Cell* test, Pair* dest) {
	return;
}

int StarSearch(Room* data, Pair* src, Pair* dest) {
    // Variables used in the function
    double gNew, hNew, fNew;
    int i, j;

    // Check if the source cell is valid (within grid limits)
    if (!is_value(src->x, src->y, data->Row, data->Col)) {
        printf("Source is invalid\n");
        return -1;
    }

    // Check if the destination cell is valid (within grid limits)
    if (!is_value(dest->x, dest->y, data->Row, data->Col)) {
        printf("Destination is invalid\n");
        return -2;
    }

    // Check if either the source or the destination cell is blocked
    if (!is_unBlocked(data, src->x, src->y) || !is_unBlocked(data, dest->x, dest->y)) {
        printf("Source or the destination is blocked\n");
        return -3;
    }

    // Check if the destination cell is the same as the source cell
    if (isDestination(src->x, src->y, dest)) {
        printf("We are already at the destination\n");
        return -4;
    }

    // Allocate and initialize the closed list
    bool* closedList = (bool*)malloc(sizeof(bool) * (data->Row) * (data->Col));
    if (closedList == NULL) {
        free(closedList);
        return -1;
    }
    memset(closedList, false, (data->Row) * (data->Col) * sizeof(bool));

    // Allocate and initialize the cell details
    Cell* cellDetails = (Cell*)malloc(sizeof(Cell) * data->Row * data->Col);
    if (cellDetails == NULL) {
        free(cellDetails);
        return -1;
    }
    for (j = 0; j < data->Row; j++) {
        for (i = 0; i < data->Col; i++) {
            cellDetails[data->Col * j + i].f = FLT_MAX;
            cellDetails[data->Col * j + i].g = FLT_MAX;
            cellDetails[data->Col * j + i].h = FLT_MAX;
            cellDetails[data->Col * j + i].parent_x = -1;
            cellDetails[data->Col * j + i].parent_y = -1;
        }
    }

    // Initialize the parameters of the starting node
    i = src->x;
    j = src->y;
    cellDetails[data->Col * j + i].f = 0.0;
    cellDetails[data->Col * j + i].g = 0.0;
    cellDetails[data->Col * j + i].h = 0.0;
    cellDetails[data->Col * j + i].parent_x = i;
    cellDetails[data->Col * j + i].parent_y = j;

    // Allocate and initialize the open list
    OpenListNode* openList = (OpenListNode*)malloc(sizeof(OpenListNode));
    if (openList == NULL) {
        free(openList);
        return -1;
    }
    openList->f = 0.0;
    openList->x = i;
    openList->y = j;
    openList->next = NULL;

    bool foundDest = false;  // Boolean value to indicate whether the destination is found

    // Main loop of the A* algorithm
    while (openList != NULL) {
        OpenListNode p = *openList;

        // Add the current node to the closed list
        i = p.x;
        j = p.y;
        closedList[data->Col * j + i] = true;

        // Check the successors (North, South, East, West)

        // 1st Successor (North)
        if (is_value(i - 1, j, data->Row, data->Col)) {
            if (isDestination(i - 1, j, dest)) {
                cellDetails[data->Col * j + (i - 1)].parent_x = i;
                cellDetails[data->Col * j + (i - 1)].parent_y = j;
                foundDest = true;
            }
            else if (!closedList[data->Col * j + (i - 1)] && is_unBlocked(data, i - 1, j)) {
                gNew = cellDetails[data->Col * j + i].g + 1.0;
                hNew = calcule_heuristiques(i - 1, j, dest);
                fNew = gNew + hNew;

                if (cellDetails[data->Col * j + (i - 1)].f == FLT_MAX || cellDetails[data->Col * j + (i - 1)].f > fNew) {
                    insertOpenList(openList, fNew, i - 1, j);
                    cellDetails[data->Col * j + (i - 1)].f = fNew;
                    cellDetails[data->Col * j + (i - 1)].g = gNew;
                    cellDetails[data->Col * j + (i - 1)].h = hNew;
                    cellDetails[data->Col * j + (i - 1)].parent_x = i;
                    cellDetails[data->Col * j + (i - 1)].parent_y = j;
                }
            }
        }

        // 2nd Successor (South)
        if (is_value(i, j + 1, data->Row, data->Col)) {
            if (isDestination(i, j + 1, dest)) {
                cellDetails[data->Col * (j + 1) + i].parent_x = i;
                cellDetails[data->Col * (j + 1) + i].parent_y = j;
                foundDest = true;
            }
            else if (!closedList[data->Col * (j + 1) + i] && is_unBlocked(data, i, j + 1)) {
                gNew = cellDetails[data->Col * j + i].g + 1.0;
                hNew = calcule_heuristiques(i, j + 1, dest);
                fNew = gNew + hNew;

                if (cellDetails[data->Col * (j + 1) + i].f == FLT_MAX || cellDetails[data->Col * (j + 1) + i].f > fNew) {
                    insertOpenList(openList, fNew, i, j + 1);
                    cellDetails[data->Col * (j + 1) + i].f = fNew;
                    cellDetails[data->Col * (j + 1) + i].g = gNew;
                    cellDetails[data->Col * (j + 1) + i].h = hNew;
                    cellDetails[data->Col * (j + 1) + i].parent_x = i;
                    cellDetails[data->Col * (j + 1) + i].parent_y = j;
                }
            }
        }

        // 3rd Successor (East)
        if (is_value(i + 1, j, data->Row, data->Col)) {
            if (isDestination(i + 1, j, dest)) {
                cellDetails[data->Col * j + (i + 1)].parent_x = i;
                cellDetails[data->Col * j + (i + 1)].parent_y = j;
                foundDest = true;
            }
            else if (!closedList[data->Col * j + (i + 1)] && is_unBlocked(data, i + 1, j)) {
                gNew = cellDetails[data->Col * j + i].g + 1.0;
                hNew = calcule_heuristiques(i + 1, j, dest);
                fNew = gNew + hNew;

                if (cellDetails[data->Col * j + (i + 1)].f == FLT_MAX || cellDetails[data->Col * j + (i + 1)].f > fNew) {
                    insertOpenList(openList, fNew, i + 1, j);
                    cellDetails[data->Col * j + (i + 1)].f = fNew;
                    cellDetails[data->Col * j + (i + 1)].g = gNew;
                    cellDetails[data->Col * j + (i + 1)].h = hNew;
                    cellDetails[data->Col * j + (i + 1)].parent_x = i;
                    cellDetails[data->Col * j + (i + 1)].parent_y = j;
                }
            }
        }

        // 4th Successor (West)
        if (is_value(i, j - 1, data->Row, data->Col)) {
            if (isDestination(i, j - 1, dest)) {
                cellDetails[data->Col * (j - 1) + i].parent_x = i;
                cellDetails[data->Col * (j - 1) + i].parent_y = j;
                foundDest = true;
            }
            else if (!closedList[data->Col * (j - 1) + i] && is_unBlocked(data, i, j - 1)) {
                gNew = cellDetails[data->Col * j + i].g + 1.0;
                hNew = calcule_heuristiques(i, j - 1, dest);
                fNew = gNew + hNew;

                if (cellDetails[data->Col * (j - 1) + i].f == FLT_MAX || cellDetails[data->Col * (j - 1) + i].f > fNew) {
                    insertOpenList(openList, fNew, i, j - 1);
                    cellDetails[data->Col * (j - 1) + i].f = fNew;
                    cellDetails[data->Col * (j - 1) + i].g = gNew;
                    cellDetails[data->Col * (j - 1) + i].h = hNew;
                    cellDetails[data->Col * (j - 1) + i].parent_x = i;
                    cellDetails[data->Col * (j - 1) + i].parent_y = j;
                }
            }
        }

        // Remove the processed node from the open list
        openList = removeOpenList(openList);
    }

    // Check if the destination was found
    if (foundDest == true) {
        /*printf("\nDestination found\n");*/
        return 1;
    }

    if (!foundDest) {
        return 0;
    }
}




//------------------------------------------function de recherche pour les mode avanture---------------//
void recherche(Room* data) {
	int information;
	printf("col%d\n", data->Col); 
	printf("Row%d\n", data->Row);


	for (int j = 0; j < data->Row; j++) {
		printf("\n");

		for (int i = 0; i < data->Col; i++){
			
			information = data->tab[data->Col*j+i];
		



			if (information == STAIRS_DOWN) {

				data->src->x = i;
				data->src->y = j;
			}
			if (information == STAIRS_UP) {
				
				data->dest->x= i;
				data->dest->y = j;
			}
			

		}
	}
	return;
}