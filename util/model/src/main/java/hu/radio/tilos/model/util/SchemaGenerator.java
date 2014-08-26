
package hu.radio.tilos.model.util;

import java.io.File;
import java.net.URL;
import java.util.ArrayList;
import java.util.List;
import java.util.Properties;

import hu.radio.tilos.model.Mix;
import org.hibernate.cfg.AnnotationConfiguration;
import org.hibernate.cfg.Configuration;
import org.hibernate.jpa.boot.internal.EntityManagerFactoryBuilderImpl;
import org.hibernate.jpa.boot.internal.ParsedPersistenceXmlDescriptor;
import org.hibernate.tool.hbm2ddl.SchemaExport;
import org.hibernate.tool.hbm2ddl.SchemaUpdate;

public class SchemaGenerator {
    private AnnotationConfiguration cfg;

    public SchemaGenerator(String packageName) throws Exception {
        cfg = new AnnotationConfiguration();
        cfg.setProperty("hibernate.hbm2ddl.auto", "create");

        for (Class<Object> clazz : getClasses(packageName)) {
            cfg.addAnnotatedClass(clazz);
        }
    }

    /**
     * @param args
     */
    public static void main(String[] args) throws Exception {
        SchemaGenerator gen = new SchemaGenerator("hu.radio.tilos.model");
        gen.generate(Dialect.MYSQL);

    }

    List<Class> getClasses(String packageName) throws Exception {
        List<Class> classes = new ArrayList<Class>();
        File directory = null;
        try {
            ClassLoader cld = Thread.currentThread().getContextClassLoader();
            if (cld == null) {
                throw new ClassNotFoundException("Can't get class loader.");
            }
            String path = packageName.replace('.', '/');
            URL resource = cld.getResource(path);
            if (resource == null) {
                throw new ClassNotFoundException("No resource for " + path);
            }
            directory = new File(resource.getFile());
        } catch (NullPointerException x) {
            throw new ClassNotFoundException(packageName + " (" + directory
                    + ") does not appear to be a valid package");
        }
        if (directory.exists()) {
            String[] files = directory.list();
            for (int i = 0; i < files.length; i++) {
                if (files[i].endsWith(".class")) {
                    // removes the .class extension
                    classes.add(Class.forName(packageName + '.'
                            + files[i].substring(0, files[i].length() - 6)));
                }
            }
        } else {
            throw new ClassNotFoundException(packageName
                    + " is not a valid package");
        }

        return classes;
    }

    /**
     * Method that actually creates the file.
     *
     * @param dialect to use
     */
    private void generate(Dialect dialect) throws Exception {
        cfg.setProperty("hibernate.dialect", dialect.getDialectClass());

        Properties properties = new Properties();
        ParsedPersistenceXmlDescriptor pud = new ParsedPersistenceXmlDescriptor(SchemaGenerator.class.getResource("META-INF"));
        pud.setName("tilos");
        pud.addMappingFiles("META-INF/persistence.xml");
        //pud.addClasses(Resources.getClasseNames(packageName));
        //pud.addMappingFiles("META-INF/persistence.xml");
        List<String> clazzes = new ArrayList<String>();
        for (Class cl : getClasses("hu.radio.tilos.model")) {
            clazzes.add(cl.getCanonicalName());
        }
        pud.addClasses(clazzes);
        properties.setProperty("hibernate.dialect", dialect.getDialectClass());
        properties.setProperty("hibernate.connection.url", "jdbc:mysql://localhost:3306/tilos2");
        properties.setProperty("hibernate.connection.driver_class", "com.mysql.jdbc.Driver");
        properties.setProperty("hibernate.connection.username", "root");
        properties.setProperty("javax.persistence.jdbc.password", "");

        //ValidatorFactory validatorFactory = Validation.buildDefaultValidatorFactory();
        EntityManagerFactoryBuilderImpl factoryBuilder = new EntityManagerFactoryBuilderImpl(pud, properties);
        //withValidatorFactory(validatorFactory).
        factoryBuilder.build().close(); // create HibernateConfiguration instance
        //this.injectBeanValidationConstraintToDdlTranslator();
        //validatorFactory.close();

        Configuration cfg = factoryBuilder.getHibernateConfiguration();
        cfg.setProperty("hibernate.hbm2ddl.auto", "update");


        SchemaUpdate export = new SchemaUpdate(cfg);
        export.setDelimiter(";");
        export.setOutputFile("ddl_" + dialect.name().toLowerCase() + ".sql");

        export.execute(true, false);
    }

    /**
     * Holds the classnames of hibernate dialects for easy reference.
     */
    private static enum Dialect {
        ORACLE("org.hibernate.dialect.Oracle10gDialect"),
        MYSQL("org.hibernate.dialect.MySQLDialect"),
        HSQL("org.hibernate.dialect.HSQLDialect");

        private String dialectClass;

        private Dialect(String dialectClass) {
            this.dialectClass = dialectClass;
        }

        public String getDialectClass() {
            return dialectClass;
        }
    }
}
