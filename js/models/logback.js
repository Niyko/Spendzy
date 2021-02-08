class LogbackModel extends Model {
    config() {
        return {
            table: "logback"
        };
    }

    globalFilter(item){
        return true;
    }
}