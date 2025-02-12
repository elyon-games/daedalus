#include "pch.h"
#include "../solver_vs/main.h"


TEST(calculate_heuristiques, heuristiques) {

	int i = 1; 
	int j = 1; 
	Pair* dest = (Pair*)malloc(sizeof(Pair));
	if(dest == NULL) { free(dest);  }

	EXPECT_EQ(20, calcule_heuristiques(i, j, dest));
	EXPECT_TRUE(20 == calcule_heuristiques(i, j, dest));
}