package main

import (
	"bufio"
	"database/sql"
	"fmt"
	"log"
	"net"
	"os"
	"strings"
	"time"

	_ "github.com/go-sql-driver/mysql"
)

const (
	amiHost     = "localhost"    // Адрес вашего сервера Asterisk
	amiPort     = "5038"         // Порт для подключения к AMI
	amiUsername = "asterisk_ami" // Имя пользователя для подключения
	amiPassword = "AAJX8xFFFxs"  // Пароль для подключения
)

var db *sql.DB

// Структура для события, которое будем сохранять в базу
type AsteriskEvent struct {
	Event    string
	CallerID string
	Exten    string
	Channel  string
	Context  string
}

func main() {
	// Подключение к AMI
	conn, err := net.Dial("tcp", fmt.Sprintf("%s:%s", amiHost, amiPort))
	if err != nil {
		fmt.Printf("Error connect to server AMI: %v\n", err)
		os.Exit(1)
	}
	defer conn.Close()

	fmt.Println("Connect is successful to AMI")

	// Аутентификация в AMI
	loginCommand := fmt.Sprintf("Action: Login\r\nUsername: %s\r\nSecret: %s\r\n\r\n", amiUsername, amiPassword)
	_, err = conn.Write([]byte(loginCommand))
	if err != nil {
		fmt.Printf("Error Login: %v\n", err)
		os.Exit(1)
	}

	// Подключение к базе данных
	err = connectToDB()
	if err != nil {
		log.Fatalf("Error connecting to the database: %v", err)
	}
	defer db.Close()

	// Обработка ответов
	readResponse(conn)
	registerEvent(conn)

	// Главный цикл получения событий
	for {
		readResponse(conn)
		time.Sleep(100 * time.Millisecond)
	}
}

// Подключение к базе данных
func connectToDB() error {
	var err error
	// Здесь можно использовать переменные из окружения для конфигурации
	dsn := "root:AAJX8xFFFxs@tcp(127.0.0.1:3306)/asterisk"
	db, err = sql.Open("mysql", dsn)
	if err != nil {
		return fmt.Errorf("Error opening database: %v", err)
	}

	// Проверяем подключение
	err = db.Ping()
	if err != nil {
		return fmt.Errorf("Error pinging database: %v", err)
	}

	fmt.Println("Database connected successfully.")
	return nil
}

// Чтение ответа от сервера AMI
func readResponse(conn net.Conn) {
	reader := bufio.NewReader(conn)
	response, err := reader.ReadString('\n')
	if err != nil {
		fmt.Printf("Error reading response: %v\n", err)
		return
	}

	response = strings.TrimSpace(response)
	fmt.Println("Response from Asterisk:", response)

	// Пример: обработка события
	if strings.Contains(response, "Event:") {
		handleEvent(response)
	}
}

// Обработка события и сохранение его в базу данных
func handleEvent(response string) {
	// Извлечение данных события
	eventData := parseEventData(response)

	// Создаем структуру события
	event := AsteriskEvent{
		Event:    eventData["Event"],
		CallerID: eventData["CallerIDNum"],
		Exten:    eventData["Exten"],
		Channel:  eventData["Channel"],
		Context:  eventData["Context"],
	}

	// Вставка в базу данных
	err := insertEventIntoDB(event)
	if err != nil {
		log.Printf("Error inserting event into DB: %v\n", err)
	} else {
		fmt.Println("Event saved successfully!")
	}
}

// Парсинг данных из строки события (можно улучшить в зависимости от формата)
func parseEventData(response string) map[string]string {
	eventData := make(map[string]string)
	lines := strings.Split(response, "\n")

	for _, line := range lines {
		if strings.Contains(line, ":") {
			parts := strings.SplitN(line, ":", 2)
			if len(parts) == 2 {
				eventData[strings.TrimSpace(parts[0])] = strings.TrimSpace(parts[1])
			}
		}
	}

	return eventData
}

// Вставка события в базу данных
func insertEventIntoDB(event AsteriskEvent) error {
	query := `INSERT INTO asterisk_logs (event, callerid, exten, channel, context, created_at) 
			  VALUES (?, ?, ?, ?, ?, NOW())`

	_, err := db.Exec(query, event.Event, event.CallerID, event.Exten, event.Channel, event.Context)
	return err
}

func registerEvent(conn net.Conn) {
	// Пример регистрации на событие
	eventCommand := "Action: Eventlist\r\n\r\n"
	_, err := conn.Write([]byte(eventCommand))
	if err != nil {
		fmt.Printf("Error sending Eventlist command: %v\n", err)
		return
	}
	fmt.Println("Eventlist command sent.")
}
