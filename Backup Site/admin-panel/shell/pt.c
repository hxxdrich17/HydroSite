#include <unistd.h>
#include <sys/select.h>
#include <stdio.h>
#ifdef __LINUX__
#include <pty.h>
#else
#include <util.h>
#endif

static void set_fds(fd_set *reads, int pttyno) {
	FD_ZERO(reads);
	FD_SET(0, reads);
	FD_SET(pttyno, reads);
}

int main(int argc, char *argv[]) {
	char buf[1024];
	int pttyno, n = 0;
	int pid;
	struct winsize winsz;
	
	if (argc < 3) {
		fprintf(stderr, "Usage: %s <rows> <cols> <cmd> [args]\n", argv[0]);
		return 1;
	}
	
	winsz.ws_row = atoi(argv[1]);
	winsz.ws_col = atoi(argv[2]);
	winsz.ws_xpixel = winsz.ws_col * 14;
	winsz.ws_ypixel = winsz.ws_row * 14;
	
	pid = forkpty(&pttyno, NULL, NULL, &winsz);
	if (pid < 0) {
		perror("Cannot forkpty");
		return 1;
	} else if (pid == 0) {
		execvp(argv[3], argv + 3);
		perror("Cannot exec bash");
	}
	
	fd_set reads;
	set_fds(&reads, pttyno);
	
	while (select(pttyno + 1, &reads, NULL, NULL, NULL)) {
		if (FD_ISSET(0, &reads)) {
			n = read(0, buf, sizeof buf);
			if (n == 0) {
				return 0;
			} else if (n < 0) {
				perror("Could not read from stdin");
				return 1;
			}
			write(pttyno, buf, n);
		}
		
		if (FD_ISSET(pttyno, &reads)) {
			n = read(pttyno, buf, sizeof buf);
			if (n == 0) {
				return 0;
			} else if (n < 0) {
				perror("Cannot read from ptty");
				return 1;
			}
			write(1, buf, n);
		}
		
		set_fds(&reads, pttyno);
	}
	
	int statloc;
	wait(&statloc);
	
	return 0;
}
