code = '''alpha
beta
gamma
delta
epsilon
zeta
eta
theta
iota
kappa
lambda
mu
nu
xi
omicron
pi
rho
sigma
tau
upsilon
phi
chi
psi
omega'''.split('\n')

o = ['Dakota','O2','O3']
b = ['B1','B2','B3','B4']
d = ['D1','D2','D3','D4']
k = ['K1','K2','K3','K4']
s = ['S1']
import os
from random import randrange as rr
used = set()
os.mkdir('people/all')
for i in 'obdks':
    os.mkdir('people/'+i)
    for j in eval(i):
        z=rr(10,100)
        while z in used:z=rr(10,100)
        with open('people/'+i+'/'+j,'w+') as f:
            f.write(str(z))
        with open('people/all/'+j,'w+') as f:
            f.write(i)
        with open('codenames/' + str(z),'w+') as f:
            f.write(j)
        with open('lookup/' + j,'w+') as f:
            f.write(str(z))


