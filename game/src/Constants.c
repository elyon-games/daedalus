#include "main.h"

Room* new_Room(int row, int col) {
	int* mazeArray = (int*)malloc(row * col * sizeof(int));
	if (mazeArray == NULL) {
		fprintf(stderr, "Échec de l'allocation de mémoire pour mazeArray.\n");
		return NULL;
	}

	Room* tmp = (Room*)malloc(sizeof(Room));
	if (tmp == NULL) {
		free(mazeArray);
		fprintf(stderr, "Échec de l'allocation de mémoire pour Room.\n");
		return NULL;
	}

	tmp->tab = mazeArray;
	tmp->info = NULL;
	tmp->Row = row;
	tmp->Col = col;
	tmp->Key_Red = -1;
	tmp->Key_Blue = -1;
	tmp->Key_Green = -1;
	tmp->Key_Yellow = -1;
	tmp->Cut = -1;
	tmp->elec = ELECTRIC_ON; 

	Pair* tmps = (Pair*)malloc(sizeof(Pair));
	Pair* tmpd = (Pair*)malloc(sizeof(Pair));
	if (tmps == NULL || tmpd == NULL) {
		free(tmps);
		free(tmpd);
		free(mazeArray);
		free(tmp);
		fprintf(stderr, "Échec de l'allocation de mémoire pour Pair.\n");
		return NULL;
	}

	tmp->src = tmps;
	tmp->dest = tmpd;
	tmp->src->x = -1;
	tmp->src->y = -1;
	tmp->src->type = -1;
	tmp->dest->x = -1;
	tmp->dest->y = -1;
	tmp->dest->type = -1;

	return tmp;
}





// Imprimer le labyrinthe
void printMaze(Room* room) {
	if (room == NULL || room->tab == NULL) {
		fprintf(stderr, "Invalid room or labyrinth array.\n");
		return;
	}

	int width = room->Col;
	int height = room->Row;
	int* maze = room->tab;
	printf("["); 
	for (int y = 0; y < height; y++) {
		for (int x = 0; x < width; x++) {
			printf("%3d,", maze[y * width + x]);
		}
		/*printf("\n");*/
	}
	printf("]");
}

// Libérer la mémoire du labyrinthe
void freeRoom(Room* room) {
	if (room != NULL) {
		free(room->tab);
		free(room->src);
		free(room->dest);
		free(room);
	}
}