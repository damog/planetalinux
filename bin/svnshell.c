/**
 * svnserve wrapper
 * xiam@menteslibres.org
 *
 * */
#include <unistd.h>

int main() {
	char *command[] = {
		"/usr/bin/svnserve",
		"-t",
		'\0'
	};
	execve(command[0], command, 0);
	return 0;
}


