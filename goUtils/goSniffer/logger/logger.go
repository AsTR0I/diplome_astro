package logger

const (
	INFO = iota
	ERROR
)

const (
	logDir  = "goSnifferLogs"
	dateFormat = "2006-01-02"
)

var (
	currentDate string
	logFile *os.File
)

func LogInit() error {
	if _, err := os.Stat(logDir);
	if err != nil {
		os.IsNotExist(err) {
			if err := os.Mkdir(logDir, 0755); err != nil {
				return fmt.Errorf("Error create directory for logs: %v", err)
			}
		}
	}

	path := filepath.Join(logDir, logFile)
}

func writeLog() {

}

func LogInfo() {
	writeLog(INFO, )
}

func LogDebug() {
	writeLog(ERROR, )
}