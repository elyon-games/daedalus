
#include"main.h"


void initUnionFind(int* parent, int* rank, int size) {
    for (int i = 0; i < size; i++) {
        parent[i] = i;
        rank[i] = 0;
    }
}

int find(int* parent, int x) {
    if (parent[x] != x) {
        parent[x] = find(parent, parent[x]); // Path compression
    }
    return parent[x];
}


void unionSets(int* parent, int* rank, int x, int y) {
    int rootX = find(parent, x);
    int rootY = find(parent, y);

    if (rootX != rootY) {
        if (rank[rootX] > rank[rootY]) {
            parent[rootY] = rootX;
        }
        else if (rank[rootX] < rank[rootY]) {
            parent[rootX] = rootY;
        }
        else {
            parent[rootY] = rootX;
            rank[rootX]++;
        }
    }
}


void shuffleEdges(Wall* edges, int size) {
    for (int i = size - 1; i > 0; i--) {
        int j = rand() % (i + 1);
        Wall temp = edges[i];
        edges[i] = edges[j];
        edges[j] = temp;
    }
}

void generateMaze(Room* room) {
    if (room == NULL || room->tab == NULL) {
        fprintf(stderr, "Invalid room or labyrinth array.\n");
        return;
    }

    


    // Initialize maze with walls
    for (int y = 0; y < room->Row; y++) {
        for (int x = 0; x < room->Col; x++) {
            room->tab[y * room->Col + x] = WALL;
        }
    }

    // Create edges for the maze
    int numEdges = 0;
    Wall* edges = (Wall*)malloc((room->Col / 2) * (room->Row / 2) * 4 * sizeof(Wall));
    if (edges == NULL) {
        fprintf(stderr, "Memory allocation failed for edges.\n");
        return;
    }
    for (int y = 1; y < room->Row - 1; y += 2) {
        for (int x = 1; x < room->Col - 1; x += 2) {
            if (x + 2 < room->Col) {
                edges[numEdges++] = (Wall){ x, y, x + 2, y };
            }
            if (y + 2 < room->Row) {
                edges[numEdges++] = (Wall){ x, y, x, y + 2 };
            }
        }
    }

    shuffleEdges(edges, numEdges);

    // Initialize Union-Find structures
    int* parent = (int*)malloc(room->Col * room->Row * sizeof(int));
    int* rank = (int*)malloc(room->Col * room->Row * sizeof(int));
    if (parent == NULL || rank == NULL) {
        fprintf(stderr, "Memory allocation failed for union-find structures.\n");
        free(edges);
        return;
    }
    initUnionFind(parent, rank, room->Col * room->Row);

    // Create maze by connecting cells
    for (int i = 0; i < numEdges; i++) {
        Wall e = edges[i];
        int cell1 = e.y1 * room->Col + e.x1;
        int cell2 = e.y2 * room->Col + e.x2;

        if (find(parent, cell1) != find(parent, cell2)) {
            room->tab[((e.y1 + e.y2) / 2) * room->Col + (e.x1 + e.x2) / 2] = GROUND;
            room->tab[e.y1 * room->Col + e.x1] = GROUND;
            room->tab[e.y2 * room->Col + e.x2] = GROUND;

            unionSets(parent, rank, cell1, cell2);
        }
    }

    free(edges);
    free(parent);
    free(rank);

    // Ensure the maze is fully enclosed by walls
    for (int y = 0; y < room->Row; y++) {
        room->tab[y * room->Col] = WALL;
        room->tab[y * room->Col + room->Col - 1] = WALL;
    }

    for (int x = 0; x < room->Col; x++) {
        room->tab[x] = WALL;
        room->tab[(room->Row - 1) * room->Col + x] = WALL;
    }
}
 
void addnewliste_objet(Room* data, int i, int j, int objet) {
    if (data->info == NULL) {
        data->info = (Pair*)malloc(20 * sizeof(Pair));
        if (data->info == NULL) {
            fprintf(stderr, "Memory allocation failed for the object list.\n");
            return;
        }
        data->info[0].x = i;
        data->info[0].y = j;
        data->info[0].type = objet;
        data->info[1].x = -1;
        return;
    }

    int w = 0;
    while (data->info[w].x != -1) {
        w++;
        if (w >= 20) {
            fprintf(stderr, "No more space available in the array.\n");
            return;
        }
    }

    data->info[w].x = i;
    data->info[w].y = j;
    data->info[w].type = objet;
    if (w + 1 < 20) {
        data->info[w + 1].x = -1;
    }

   
}


void addObjectToMaze(Room* data, int object) {
    if (data == NULL || data->tab == NULL) {
        fprintf(stderr, "Invalid room or labyrinth array.\n");
        return;
    }

    
    int numPathCells = 0;
    for (int y = 0; y < data->Row; y++) {
        for (int x = 0; x < data->Col; x++) {
            if (data->tab[y * data->Col + x] == GROUND) {
                numPathCells++;
            }
        }
    }

    if (numPathCells > 0) {
        int randomIndex = rand() % numPathCells;
        int count = 0;
        for (int y = 0; y < data->Row; y++) {
            for (int x = 0; x < data->Col; x++) {
                if (data->tab[y * data->Col + x] == GROUND) {
                    if (count == randomIndex) {
                        data->tab[y * data->Col + x] = object;

                        switch (object) {
                        case STAIRS_DOWN:
                            data->src->x = x;
                            data->src->y = y;
                            addnewliste_objet(data, x, y, object);
                            return;
                        case STAIRS_UP:
                            data->dest->x = x;
                            data->dest->y = y;
                            addnewliste_objet(data, x, y, object);
                            return;

                        case FENCE:
                        case FENCE_ELECTRIFIED:
                        case DOOR_RED_CLOSED:
                        case DOOR_GREEN_CLOSED:
                        case DOOR_BLUE_CLOSED:
                        case DOOR_YELLOW_CLOSED:
                            return;

                        default:
                            addnewliste_objet(data, x, y, object);
                            return;
                        }

                        
                    }
                    count++;
                }
            }
        }
    }
    else {
        fprintf(stderr, "No available path cell found to add object.\n");
    }
}
