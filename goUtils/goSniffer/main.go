package main

import (
	"bufio"
	"database/sql"
	"encoding/json"
	"fmt"
	"log"
	"os"
	"os/exec"
	"regexp"
	"strings"
	"time"

	_ "github.com/go-sql-driver/mysql"
)

type Config struct {
	DB struct {
		User     string `json:"user"`
		Password string `json:"password"`
		Host     string `json:"host"`
		Port     int    `json:"port"`
		Database string `json:"database"`
	} `json:"db"`
	Port      int    `json:"port"`
	Interface string `json:"interface"`
	LogFile   string `json:"logfile"`
}

type SipPacket struct {
	CallID     string
	Method     string
	FullPacket string
	CapturedAt time.Time
}

func main() {
	// load config
	config, err := loadConfig("config/app.json")
	if err != nil {
		log.Fatal("Error loading config:", err)
	}

	// db connection
	db, err := sql.Open("mysql", fmt.Sprintf("%s:%s@tcp(%s:%d)/%s", config.DB.User, config.DB.Password, config.DB.Host, config.DB.Port, config.DB.Database))
	if err != nil {
		log.Fatal("DB error:", err)
	}
	defer db.Close()

	// start tcpdump
	cmd := exec.Command("sudo", "tcpdump", "-l", "-A", "-i", config.Interface, "udp", "port", fmt.Sprintf("%d", config.Port))
	stdout, err := cmd.StdoutPipe()
	if err != nil {
		log.Fatal("stdout error:", err)
	}

	if err := cmd.Start(); err != nil {
		log.Fatal("cmd start error:", err)
	}

	scanner := bufio.NewScanner(stdout)
	var buffer []string
	for scanner.Scan() {
		line := scanner.Text()
		fmt.Println("Received packet line:", line)

		if isNewPacketStart(line) && len(buffer) > 0 {
			// Собрали весь предыдущий пакет — сохраняем
			packetText := strings.Join(buffer, "\n")
			processAndSavePacket(packetText, db)
			buffer = []string{}
		}

		buffer = append(buffer, line)
	}

	// Не забудьте сохранить последний пакет
	if len(buffer) > 0 {
		packetText := strings.Join(buffer, "\n")
		processAndSavePacket(packetText, db)
	}

	if len(buffer) > 0 {
		packetText := strings.Join(buffer, "\n")
		processAndSavePacket(packetText, db)
	}

	if err := scanner.Err(); err != nil {
		fmt.Println("scan error:", err)
	}
}

func loadConfig(filename string) (Config, error) {
	var config Config
	file, err := os.Open(filename)
	if err != nil {
		return config, err
	}
	defer file.Close()

	decoder := json.NewDecoder(file)
	err = decoder.Decode(&config)
	if err != nil {
		return config, err
	}

	return config, nil
}

func isNewPacketStart(line string) bool {
	return strings.Contains(line, "IP ") && strings.Contains(line, " > ") && strings.Contains(line, "UDP")
}

func extractHeader(packet, name string) string {
	re := regexp.MustCompile(name + `:\s*(.+)`)
	match := re.FindStringSubmatch(packet)
	if len(match) > 1 {
		return strings.TrimSpace(match[1])
	}
	return ""
}

func extractMethod(packet string) string {
	lines := strings.Split(packet, "\n")

	// Регулярки для первой строки SIP-запроса и строки CSeq
	reStartLine := regexp.MustCompile(`(?i)^(INVITE|ACK|BYE|CANCEL|OPTIONS|REGISTER|INFO|SUBSCRIBE|NOTIFY|MESSAGE|REFER)\s+`)
	reCSeq := regexp.MustCompile(`(?i)^CSeq:\s*\d+\s+(INVITE|ACK|BYE|CANCEL|OPTIONS|REGISTER|INFO|SUBSCRIBE|NOTIFY|MESSAGE|REFER)`)

	for _, line := range lines {
		line = strings.TrimSpace(line)

		// Если строка начинается с SIP-метода
		if matches := reStartLine.FindStringSubmatch(line); matches != nil {
			return strings.ToUpper(matches[1])
		}

		// Если строка содержит CSeq: и метод
		if matches := reCSeq.FindStringSubmatch(line); matches != nil {
			return strings.ToUpper(matches[1])
		}
	}

	return ""
}

func extractIPsFromPacket(packet string) (string, string) {
	re := regexp.MustCompile(`IP ([\d\.]+)\.\d+ > ([\d\.]+)\.\d+:`)
	match := re.FindStringSubmatch(packet)
	if len(match) == 3 {
		return match[1], match[2]
	}
	return "", ""
}

func processAndSavePacket(packet string, db *sql.DB) {
	callID := extractHeader(packet, "Call-ID")
	method := extractMethod(packet)
	timestamp := time.Now()

	sourceIP, destIP := extractIPsFromPacket(packet)

	_, err := db.Exec(`INSERT INTO sip_packets (call_id, source_ip, dest_ip, method, full_packet, captured_at)
					   VALUES (?, ?, ?, ?, ?, ?)`,
		callID, sourceIP, destIP, method, packet, timestamp)

	if err != nil {
		fmt.Println("DB insert error:", err)
	} else {
		fmt.Println("Saved packet: Call-ID=%s, Method=%s\n", callID, method)
	}
}
