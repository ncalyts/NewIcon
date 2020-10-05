# Created By Neil Clayton
# Date 4/10/20
# Quick prototype test for theory on the Rubiks cube program
# b= blue, g = green y = yellow w = white

import numpy as np
import copy

# set four faces of colours A B C D
A = np.array([
    ['b', 'b', 'b'],
    ['b', 'b', 'b'],
    ['b', 'b', 'b']
])

B = np.array([
    ['g', 'g', 'g'],
    ['g', 'g', 'g'],
    ['g', 'g', 'g']
])

C = np.array([
    ['y', 'y', 'y'],
    ['y', 'y', 'y'],
    ['y', 'y', 'y']
])


D = np.array([
    ['w', 'w', 'w'],
    ['w', 'w', 'w'],
    ['w', 'w', 'w']
])

# function to simulate rotation on one axis in both directions
def rotateRubiks(row, direction):
    move1 = copy.deepcopy(A[row])
    move2 = copy.deepcopy(B[row])
    move3 = copy.deepcopy(C[row])
    move4 = copy.deepcopy(D[row])

    if direction == "CLOCKWISE":
        B[row] = move1
        C[row] = move2
        D[row] = move3
        A[row] = move4
    elif direction == "ANTI":
        D[row] = move1
        C[row] = move4
        B[row] = move3
        A[row] = move2


# Check if values on one face all match
def allElementsEqual(face):
    result = np.all(face == face[0][0])
    if result:
        print('All values match')
    else:
        print("All values do not match")

# test starting setup, should return match
allElementsEqual(A)

# perform a clockwise rotation on index 1
rotateRubiks(1, "CLOCKWISE")
#print resulting faces
print("===CLOCKWISE===")
print("A", A)
print("B", B)
print("C", C)
print("D", D)
# check A face matches, should be false
allElementsEqual(A)

# perform a anti clockwise rotation on index 2
rotateRubiks(2, "ANTI")
#print resulting faces
print("===ANTICLOCKWISE===")
print("A", A)
print("B", B)
print("C", C)
print("D", D)

# check A face matches, should be false
allElementsEqual(A)

print("returning rotations to original state and all sides should match")
# Counter act the rotations done to return to original state
rotateRubiks(1, "ANTI")
rotateRubiks(2, "CLOCKWISE")

#print resulting faces
print("===Undone rotations===")
print("A", A)
print("B", B)
print("C", C)
print("D", D)

# test starting setup, should return match
allElementsEqual(A)

